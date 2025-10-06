<?php

namespace App\Http\Controllers;

use App\Models\Offices;
use Illuminate\Http\Request;

class OfficesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
          'office_code' => 'required|string|max:255|unique:offices',
          'office_name' => 'required|string|max:255',
          'office_acr' => 'required|string|max:255',
          'user_id' => 'required|integer',
          'role_id' => 'required|integer',
          'office_pap_code' => 'nullable|string|max:255',
          'office_pap_no' => 'nullable|string|max:255',
          'office_c_show' => 'required|boolean',
          'office_c_is_college' => 'required|boolean',
          'office_c_is_one' => 'required|boolean',
        ]);

        $office = Offices::create($fields);

        return response()->json($office, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offices $offices)
    {
        $offices = Offices::all();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offices $offices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offices $offices)
    {
        //
    }
}
