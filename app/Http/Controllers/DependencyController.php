<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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


    /*LA LOGICA DE USUARIOS DE LA DEPENDENCIA EMPIEZA*/

    public function usersIndex(Dependency $dependency)
    {
        $dependency = Dependency::where('id', $dependency->id)->with('users')->first();

        $allUsers = User::all();
        return Inertia::render('dependencies/AddUsers', [
            'dependency' => $dependency,
            'users' => $dependency->users,
            'allUsers' => $allUsers
        ]);
    }

    public function usersStore(Dependency $dependency, Request $request)
    {

        $userCount = DB::table('dependency_user')
            ->where('user_id', '=', $request->input('userId'))
            ->where('dependency_id', '=', $dependency->id)
            ->count();

        if ($userCount > 0) {
            return response('El usuario ya pertenece a la dependencia. Si desea cambiar el rol, eliminelo y vuelvalo a agregar a la dependencia', 403);
        }

        $user = User::find($request->input('userId'));
        $user->dependencies()->attach($dependency->id, ['role' => $request->input('roleId')]);

        return response('Usuario asignado correctamente a la dependencia', 200);

    }

    public function usersDelete($dependencyId, $userId)
    {
        DB::table('dependency_user')
            ->where('user_id', '=', $userId)
            ->where('dependency_id', '=', $dependencyId)
            ->delete();

        return response('Usuario eliminado correctamente de la dependencia', 200);

    }
}
