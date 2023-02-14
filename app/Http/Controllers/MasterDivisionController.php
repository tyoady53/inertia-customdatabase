<?php

namespace App\Http\Controllers;

use App\Models\MasterDivision;
use App\Http\Requests\StoreMasterDivisionRequest;
use App\Http\Requests\UpdateMasterDivisionRequest;

class MasterDivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMasterDivisionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMasterDivisionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterDivision  $masterDivision
     * @return \Illuminate\Http\Response
     */
    public function show(MasterDivision $masterDivision)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MasterDivision  $masterDivision
     * @return \Illuminate\Http\Response
     */
    public function edit(MasterDivision $masterDivision)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMasterDivisionRequest  $request
     * @param  \App\Models\MasterDivision  $masterDivision
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMasterDivisionRequest $request, MasterDivision $masterDivision)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasterDivision  $masterDivision
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterDivision $masterDivision)
    {
        //
    }
}
