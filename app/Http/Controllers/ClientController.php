<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // Récupérer tous les clients
        $clients = Client::all();

        return response()->json($clients);
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);

        return response()->json($client);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email',
            'num_phone' => 'required|string|max:15|unique:clients,num_phone',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'num_phone' => $request->num_phone,
            'password' => Hash::make($request->password),
            'type_user_id' => 3, // ID du type d'utilisateur "client"
            'actif' => 1,
            'password_updated_at' => now(),
            'two_factor_enabled' => 0,
            'is_commercial' => 0,
            'stock_journal' => 0,
            'image' => null,
            'point_of_sale_id' => null,
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $client = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'num_phone' => $request->num_phone,
            'password' => Hash::make($request->password),
            'user_id' => $user->id,
            'commercial_affiliate_id' => Auth::user()->id
        ]);


        return response()->json($client, 201);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($client->user_id);
        $user->delete();
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }
    /**
     * Mettre à jour un client existant.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:clients,email,' . $client->id,
            'num_phone' => 'sometimes|string|max:15|unique:clients,num_phone,' . $client->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($client->user_id);
        $user->nom = $request->nom ?? $user->nom;
        $user->prenom = $request->prenom ?? $user->prenom;
        $user->email = $request->email ?? $user->email;
        $user->num_phone = $request->num_phone ?? $user->num_phone;

        $client->update($request->all());
        return response()->json($client);
    }

}
