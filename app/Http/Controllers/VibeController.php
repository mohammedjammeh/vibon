<?php

namespace App\Http\Controllers;

use App\vibe;
use Illuminate\Http\Request;

class VibeController extends Controller
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
        return view('vibe.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vibe = new vibe();
        $vibe->title = request('title');
        $vibe->description = request('description');
        $vibe->key = '123';
        $vibe->save();


        return redirect('/home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function show(vibe $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(vibe $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vibe $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(vibe $id)
    {
        //
    }
}
