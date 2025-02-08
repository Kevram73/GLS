<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Envoyer un message dans une conversation.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required_without:file|string',
            'file' => 'nullable|file|max:2048', // Fichier optionnel (2MB max)
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Upload fichier s'il existe
        $filePath = $request->hasFile('file') ? $request->file('file')->store('messages') : null;

        // Création du message
        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'file' => $filePath,
            'sent_at' => now(),
        ]);

        // Ajout du statut pour les destinataires
        $conversation = Conversation::find($request->conversation_id);
        foreach (json_decode($conversation->participants) as $recipientId) {
            if ($recipientId != Auth::id()) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json(['message' => 'Message envoyé avec succès', 'data' => $message]);
    }

    /**
     * Récupérer les messages d'une conversation.
     */
    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Vérifier si l'utilisateur appartient à la conversation
        if (!in_array(Auth::id(), json_decode($conversation->participants))) {
            return response()->json(['message' => 'Accès interdit à cette conversation'], 403);
        }

        $messages = Message::where('conversation_id', $conversationId)->orderBy('sent_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Supprimer un message (Soft delete).
     */
    public function deleteMessage($messageId)
    {
        $message = Message::where('id', $messageId)
                          ->where('sender_id', Auth::id()) // Vérifier que l'utilisateur est l'expéditeur
                          ->firstOrFail();

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

        return response()->json(['message' => 'Message marqué comme lu']);
    }

    /**
     * Récupérer les messages non lus de l'utilisateur.
     */
    public function getUnreadMessages()
    {
        $unreadMessages = MessageStatus::where('recipient_id', Auth::id())
                                       ->where('is_read', false)
                                       ->with('message')
                                       ->get();

        return response()->json(['unread_messages' => $unreadMessages]);
    }
}
