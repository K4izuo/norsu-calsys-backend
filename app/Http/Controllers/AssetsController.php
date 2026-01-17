<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Assets::query()->select('id', 'asset_type', 'asset_name', 'capacity', 'location')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $asset = Assets::create($fields);

        return response()->json($asset, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assets $assets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assets $assets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assets $assets)
    {
        //
    }
}
