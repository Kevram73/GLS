<?php

namespace App\Http\Controllers;

use App\Models\ConversMsg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Envoyer un message de l'utilisateur connecté vers un autre utilisateur.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required_without:file|string',
            'file'        => 'nullable|file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // On vérifie que l'utilisateur ne s'envoie pas un message à lui-même
        if (Auth::id() == $request->receiver_id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous envoyer un message à vous-même'], 403);
        }

        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('messages', 'public')
            : null;

        $message = ConversMsg::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $request->content,
            'file'        => $filePath ? Storage::url($filePath) : null,
            'date_sent'   => now()->toDateString(),
            'time_sent'   => now()->toTimeString(),
            'send_time'   => now(),
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data'    => $message
        ]);
    }

    /**
     * Récupérer les messages échangés entre l'utilisateur connecté et un autre utilisateur.
     */
    public function getMessages($otherUserId, Request $request)
    {
        $currentUserId = Auth::id();

        // Vérifier que l'autre utilisateur existe
        if (!User::find($otherUserId)) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $messages = ConversMsg::where(function ($query) use ($currentUserId, $otherUserId) {
                            $query->where('sender_id', $currentUserId)
                                  ->where('receiver_id', $otherUserId);
                        })
                        ->orWhere(function ($query) use ($currentUserId, $otherUserId) {
                            $query->where('sender_id', $otherUserId)
                                  ->where('receiver_id', $currentUserId);
                        })
                        ->orderBy('send_time', 'desc')
                        ->paginate(20);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Supprimer un message envoyé par l'utilisateur connecté.
     */
    public function deleteMessage($messageId)
    {
        $message = ConversMsg::where('id', $messageId)
                             ->where('sender_id', Auth::id())
                             ->firstOrFail();

        $message->delete();

        return response()->json(['message' => 'Message supprimé avec succès']);
    }

    /**
     * Récupérer la liste des utilisateurs avec lesquels l'utilisateur connecté a échangé des messages.
     */
    public function getUsersContacted()
    {
        $currentUserId = Auth::id();

        // Récupérer les identifiants des utilisateurs contactés en envoyant et en recevant des messages
        $sentMessages = ConversMsg::where('sender_id', $currentUserId)
                                  ->pluck('receiver_id')
                                  ->toArray();

        $receivedMessages = ConversMsg::where('receiver_id', $currentUserId)
                                      ->pluck('sender_id')
                                      ->toArray();

        $userIds = array_unique(array_merge($sentMessages, $receivedMessages));

        $users = User::whereIn('id', $userIds)->get();

        return response()->json(['contacted_users' => $users]);
    }
}
