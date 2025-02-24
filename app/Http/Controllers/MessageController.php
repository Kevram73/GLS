<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Events\MessageSent;
use App\Events\MessageRead;

class MessageController extends Controller
{
    /**
     * Envoyer un message dans une conversation avec WebSockets.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required_without:file|string',
            'file' => 'nullable|file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::findOrFail($request->conversation_id);
        if (!in_array(Auth::id(), json_decode($conversation->participants))) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $filePath = $request->hasFile('file') ? $request->file('file')->store('messages', 'public') : null;

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'file' => $filePath ? Storage::url($filePath) : null,
            'sent_at' => now(),
        ]);

        foreach (json_decode($conversation->participants) as $recipientId) {
            if ($recipientId != Auth::id()) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                    'is_read' => false,
                ]);
            }
        }

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => 'Message envoyé avec succès', 'data' => $message]);
    }

    /**
     * Récupérer les messages d'une conversation avec pagination.
     */
    public function getMessages($conversationId, Request $request)
    {
        $conversation = Conversation::findOrFail($conversationId);
        if (!in_array(Auth::id(), json_decode($conversation->participants))) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $messages = Message::where('conversation_id', $conversationId)
                            ->with('sender')
                            ->latest()
                            ->paginate(20);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Supprimer un message et ses statuts associés.
     */
    public function deleteMessage($messageId)
    {
        $message = Message::where('id', $messageId)->where('sender_id', Auth::id())->firstOrFail();
        MessageStatus::where('message_id', $message->id)->delete();
        $message->delete();

        return response()->json(['message' => 'Message supprimé avec succès']);
    }

    /**
     * Marquer un message comme lu.
     */
    public function markAsRead($messageId)
    {
        $status = MessageStatus::where('message_id', $messageId)
                               ->where('recipient_id', Auth::id())
                               ->firstOrFail();

        $status->update(['is_read' => true, 'read_at' => now()]);

        broadcast(new MessageRead($messageId, Auth::id()));

        return response()->json(['message' => 'Message marqué comme lu']);
    }

    /**
     * Récupérer les messages non lus avec infos expéditeur.
     */
    public function getUnreadMessages()
    {
        $unreadMessages = MessageStatus::where('recipient_id', Auth::id())
                                       ->where('is_read', false)
                                       ->with('message.sender')
                                       ->get();

        return response()->json(['unread_messages' => $unreadMessages]);
    }

    /**
     * Récupérer la liste des utilisateurs avec lesquels l'utilisateur connecté a interagi.
     */
    public function getUsersContacted()
    {
        $userIds = Message::where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id())
                          ->distinct()
                          ->pluck('recipient_id', 'sender_id')
                          ->values();

        $users = User::whereIn('id', $userIds)->get();

        return response()->json(['contacted_users' => $users]);
    }
}
