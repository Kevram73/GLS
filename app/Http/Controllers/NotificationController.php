<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    /**
     * Récupérer toutes les notifications de l'utilisateur authentifié.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Créer une nouvelle notification pour un utilisateur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type_user' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if($request->type_user == 2){
            $users = User::where('type_user_id', 2)->get();
            foreach($users as $user){
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'content' => $request->content,
                    'is_read' => false,
                ]);
            }
        } else if($request->type_user == 3){
            $users = User::where('type_user_id', 3)->get();
            foreach($users as $user){
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'content' => $request->content,
                    'is_read' => false,
                ]);
            }
        } else if($request->type_user == 4){
            $users = User::where('type_user_id', 4)->get();
            foreach($users as $user){
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'content' => $request->content,
                    'is_read' => false,
                ]);
            }
        }


        return response()->json(['message' => 'Notification créée avec succès', 'notification' => $notification], 201);
    }

    /**
     * Récupérer une notification spécifique.
     */
    public function show($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json($notification);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    /**
     * Récupérer uniquement les notifications non lues.
     */
    public function unreadNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notifications);
    }

    /**
     * Supprimer une notification.
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée avec succès']);
    }
}
