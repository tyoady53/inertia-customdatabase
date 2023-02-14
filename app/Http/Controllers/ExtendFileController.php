<?php

namespace App\Http\Controllers;

use App\Models\ExtendFile;
use App\Http\Requests\StoreExtendFileRequest;
use App\Http\Requests\UpdateExtendFileRequest;

class ExtendFileController extends Controller
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
     * @param  \App\Http\Requests\StoreExtendFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExtendFileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExtendFile  $extendFile
     * @return \Illuminate\Http\Response
     */
    public function show(ExtendFile $extendFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExtendFile  $extendFile
     * @return \Illuminate\Http\Response
     */
    public function edit(ExtendFile $extendFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExtendFileRequest  $request
     * @param  \App\Models\ExtendFile  $extendFile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExtendFileRequest $request, ExtendFile $extendFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExtendFile  $extendFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExtendFile $extendFile)
    {
        //
    }
}
