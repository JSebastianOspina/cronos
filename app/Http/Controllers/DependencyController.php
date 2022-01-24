<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DependencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {

        /*$dependency = Dependency::first();
        dd($dependency);*/

        $dependencies = Dependency::with('users')->get();
        return Inertia::render('dependencies/Index', [
            'dependencies' => $dependencies
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Dependency::create([
            'name' => $request->input('name')
        ]);

        return response('', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Dependency::destroy($id);

        return response('Dependencia borrada exitosamente', 200);
    }
}
