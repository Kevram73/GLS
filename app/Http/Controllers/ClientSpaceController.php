<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Vente;
use App\Models\Plainte;
use App\Models\Notification;
use App\Models\Journal;
use App\Models\PointOfSale;

class ClientSpaceController extends Controller
{

    public function dashboard_resume(){
        $user = Auth::user();
        $buy_number = Vente::where('client_id', $user->id)->count();
        $plaintes = Plainte::where('client_id', $user->id)->count();
        $notifications = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return response()->json([
            'buy_number' => $buy_number,
            'plaintes' => $plaintes,
            'notifications' => $notifications,
            'user' => $user
        ]);
    }

    public function buying_history()
    {
        $purchases = Vente::where('client_id', Auth::user()->id)->get();
        return response()->json($purchases);
    }

    public function vente_details($id)
    {
        $purchase = Vente::findOrFail($id);
        $seller = User::findOrFail($purchase->seller_id);
        $journal = Journal::findOrFail($purchase->journal_id);
        $point_of_sale = PointOfSale::findOrFail($purchase->point_of_sale_id);
        $status = $purchase->is_paid ? 'Paid' : 'Unpaid';

        $details = [
            'purchase' => $purchase,
            'seller' => $seller,
            'journal' => $journal,
            'point_of_sale' => $point_of_sale,
            'status' => $status,
            'created_at' => $purchase->created_at,
            'updated_at' => $purchase->updated_at,
            'total_amount' => $purchase->montant,
            'total_items' => $purchase->nbre,
            'payment_status' => $status,
            'date' => $purchase->date,
        ];

        return response()->json($details);
    }

    public function complaints()
    {
        $complaints = Plainte::where('client_id', Auth::user()->id)->get();
        return response()->json($complaints);
    }

    public function my_notifications()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)->get();
        return response()->json($notifications);
    }

    public function make_plainte(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $plainte = new Plainte();
        $plainte->title = $request->title;
        $plainte->description = $request->description;
        $plainte->client_id = Auth::user()->id;
        $plainte->register_id = Auth::user()->id;
        $plainte->save();

        return response()->json(['message' => 'Plainte créée avec succès', 'plainte' => $plainte]);
    }
}
