<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Journal;
use App\Models\Vente;

class HomeController extends Controller
{


    public function get_users(){
        $users = User::where("type_user_id", 2)->where("point_of_sale_id", null)->get();
        return response()->json(UserResource::collection($users));
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $totalClients = User::where('type_user_id', 3)->count();
        $totalClientsActifs = User::where('type_user_id', 3)->where('actif', true)->count();
        $totalJournaux = Journal::count();
        $totalVentes = Vente::count();
        $stockRestant = User::sum('stock_journal');
        $commerciaux = User::where('type_user_id', 2)->select('id', 'nom', 'prenom', 'email')->get();

        return response()->json([
            'user' => new UserResource($user),
            'total_clients' => $totalClients,
            'total_clients_actifs' => $totalClientsActifs,
            'total_journaux' => $totalJournaux,
            'total_ventes' => $totalVentes,
            'stock_restant' => $stockRestant,
            'commerciaux' => $commerciaux,
        ]);
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Ancien mot de passe incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->password_updated_at = now();
        $user->save();

        return response()->json(['message' => 'Mot de passe modifié avec succès'], 200);
    }

    public function change_user_image(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $user->image = $imageName;
        $user->save();

        return response()->json(['message' => 'Image de profil modifiée avec succès'], 200);
    }

    public function get_terms_conditions(Request $request){
        $terms = "Lorem ipsum dolor sit amet, consectetur adipiscing elit   ";
        return response()->json(['terms' => $terms]);
    }
}
