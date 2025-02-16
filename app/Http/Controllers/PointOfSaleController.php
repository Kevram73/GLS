<?php

namespace App\Http\Controllers;

use App\Http\Resources\PosResource;
use App\Http\Resources\UserResource;
use App\Models\PointOfSale;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointOfSaleController extends Controller
{
    /**
     * Lister tous les points de vente.
     */
    public function index()
    {
        $points = PointOfSale::with('manager')->get();
        return response()->json(PosResource::collection($points));
    }
    /**
     * Créer un nouveau point de vente.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:point_of_sales,name',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'required|boolean',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pointOfSale = PointOfSale::create($request->all());
        $new_user = User::find($request->manager_id);
        $new_user->point_of_sale_id = $pointOfSale->id;
        $new_user->save();
        return response()->json(new PosResource($pointOfSale), 201);
    }

    /**
     * Récupérer un point de vente spécifique.
     */
    public function show($id)
    {
        $pointOfSale = PointOfSale::with('manager')->findOrFail($id);
        return response()->json(new PosResource($pointOfSale));
    }

    /**
     * Mettre à jour un point de vente.
     */
    public function update(Request $request, $id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);


        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:point_of_sales,name,' . $id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'sometimes|boolean',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $previous_user = User::find($pointOfSale->manager_id);
        $previous_user->point_of_sale_id = null;
        $previous_user->save();

        $new_user = User::find($request->manager_id);
        $new_user->point_of_sale_id = $id;
        $new_user->save();

        $pointOfSale->update($request->all());

        return response()->json(new PosResource($pointOfSale));
    }

    /**
     * Supprimer un point de vente (Soft Delete).
     */
    public function destroy($id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);
        $pointOfSale->delete();

        return response()->json(['message' => 'Point de vente supprimé avec succès']);
    }

    /**
     * Lister les points de vente actifs.
     */
    public function activePoints()
    {
        $points = PointOfSale::where('is_active', true)->with('manager')->get();
        return response()->json(PosResource::collection($points));
    }




    /**
     * Activer un point de vente.
     */
    public function activate_point($id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);
        $pointOfSale->is_active = true;
        $pointOfSale->save();

        return response()->json(new PosResource($pointOfSale));
    }

    /**
     * Désactiver un point de vente.
     */
    public function deactivate_point($id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);
        $pointOfSale->is_active = false;
        $pointOfSale->save();

        return response()->json(new PosResource($pointOfSale));
    }
}
