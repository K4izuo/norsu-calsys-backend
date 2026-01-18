<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
  /**
   * Display assets based on user role.
   * Admins see all assets, regular users see only their own.
   */
  public function index(Request $request)
  {
    $user = $request->user();

    // Check if user is authenticated
    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated'
      ], 401);
    }

    $query = Assets::query();

    // Use the cleaner relationship method (no need to load userRole manually)
    if (!$user->canViewAllAssets()) {
      $query->where('created_by', $user->id);
    }

    $assets = $query->select(
      'id',
      'asset_type',
      'asset_name',
      'capacity',
      'location',
      'availability_status',
      'condition',
      'acquisition_date',
      'campus_id',
      'office_id',
      'created_by'
    )->orderBy('created_at', 'desc')->get();

    return response()->json($assets);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = $request->user();

    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated'
      ], 401);
    }

    $fields = $request->validate([
      'asset_name' => 'required|string|max:255',
      'asset_type' => 'required|string|max:255',
      'capacity' => 'required|integer|min:1',
      'location' => 'required|string|max:255',
      'acquisition_date' => 'required|date',
      'condition' => 'required|string|max:500',
      'campus_id' => 'required|integer|exists:campuses,id',
      'office_id' => 'required|integer|exists:offices,id',
    ]);

    $fields['availability_status'] = 'AVAILABLE';
    $fields['created_by'] = $user->id;

    $asset = Assets::create($fields);

    return response()->json($asset, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Request $request, $id)
  {
    $user = $request->user();

    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated'
      ], 401);
    }

    $asset = Assets::findOrFail($id);

    // Check if user can view this asset
    if (!$user->canViewAllAssets() && $asset->created_by !== $user->id) {
      return response()->json([
        'message' => 'Unauthorized to view this asset'
      ], 403);
    }

    return response()->json($asset);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $user = $request->user();

    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated'
      ], 401);
    }

    $asset = Assets::findOrFail($id);

    // Check if user can update this asset
    if (!$user->canViewAllAssets() && $asset->created_by !== $user->id) {
      return response()->json([
        'message' => 'Unauthorized to update this asset'
      ], 403);
    }

    $fields = $request->validate([
      'asset_name' => 'sometimes|string|max:255',
      'asset_type' => 'sometimes|string|max:255',
      'capacity' => 'sometimes|integer|min:1',
      'location' => 'sometimes|string|max:255',
      'acquisition_date' => 'sometimes|date',
      'condition' => 'sometimes|string|max:500',
      'availability_status' => 'sometimes|string|max:255',
      'campus_id' => 'sometimes|integer|exists:campuses,id',
      'office_id' => 'sometimes|integer|exists:offices,id',
    ]);

    $asset->update($fields);

    return response()->json($asset);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Request $request, $id)
  {
    $user = $request->user();

    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated'
      ], 401);
    }

    $asset = Assets::findOrFail($id);

    // Check if user can delete this asset
    if (!$user->canViewAllAssets() && $asset->created_by !== $user->id) {
      return response()->json([
        'message' => 'Unauthorized to delete this asset'
      ], 403);
    }

    $asset->delete();

    return response()->json([
      'message' => 'Asset deleted successfully'
    ], 200);
  }
}
