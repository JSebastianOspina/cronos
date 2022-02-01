<?php

namespace App\Http\Controllers;

use App\Helpers\GoogleCalendarApi;
use App\Helpers\GoogleCalendarApiException;
use App\Models\Dependency;
use App\Models\GoogleCalendar;
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
        $user = auth()->user();
        if (auth()->user()->isAdmin()) {
            //Show all dependencies to admin
            $dependencies = Dependency::with('users')->get();
        } else {
            //If not, is supervisor, just show dependencies that it supervises.
            $dependencies = DB::table('dependency_user')
                ->select(['dependencies.id', 'dependencies.name'])
                ->where('user_id', '=', $user->id)
                ->where('role', '=', 1)
                ->join('dependencies', 'dependencies.id', '=', 'dependency_user.dependency_id')
                ->get();

            foreach ($dependencies as $dependency) {
                $dependency->users = DB::table('dependency_user')
                    ->where('dependency_id', '=', $dependency->id)->get();
            }

        }


        return Inertia::render('dependencies/Index', [
            'isAdmin' => auth()->user()->isAdmin(),
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
        $dependency = Dependency::where('id', '=', $dependency->id)->with('users')->first();

        $allUsers = User::all();
        return Inertia::render('dependencies/AddUsers', [
            'dependency' => $dependency,
            'users' => $dependency->users,
            'allUsers' => $allUsers
        ]);
    }

    /**
     * @throws \JsonException
     */
    public function usersStore(Dependency $dependency, Request $request)
    {
        //Check if user belongs to the dependency
        $userCount = DB::table('dependency_user')
            ->where('user_id', '=', $request->input('userId'))
            ->where('dependency_id', '=', $dependency->id)
            ->count();

        if ($userCount > 0) {
            return response('El usuario ya pertenece a la dependencia. Si desea cambiar el rol, eliminelo y vuelvalo a agregar a la dependencia', 403);
        }
        //Get requested user
        $user = User::find($request->input('userId'));

        //Create a calendar for the user
        $googleCalendarApi = new GoogleCalendarApi();
        try {
            $calendar = $googleCalendarApi->createCalendar($dependency->name . ' - ' . $user->name);
        } catch (\RuntimeException $e) {
            return response($e->getMessage(), 403);
        }

        //Store the calendar locally
        GoogleCalendar::create([
            'google_calendar_id' => $calendar['id'],
            'url' => "https://calendar.google.com/calendar/embed?src=${calendar['id']}&ctz=America%2FBogota",
            'user_id' => $user->id,
            'dependency_id' => $dependency->id
        ]);

        //Invite the user to the calendar
        $googleCalendarApi->inviteUserToCalendar($calendar['id'], $user->email);

        //Attach the user to the dependency
        $user->dependencies()->attach($dependency->id, ['role' => $request->input('roleId')]);

        return response('Usuario asignado correctamente a la dependencia. Tambien se ha creado un calendario para el usuario', 200);

    }

    /**
     * @throws \JsonException
     */
    public function usersDelete($dependencyId, $userId)
    {
        //Remove user from dependency
        DB::table('dependency_user')
            ->where('user_id', '=', $userId)
            ->where('dependency_id', '=', $dependencyId)
            ->delete();

        //Also search and delete the calendar

        $googleCalendar = GoogleCalendar::where('user_id', '=', $userId)
            ->where('dependency_id', '=', $dependencyId)
            ->first();
        if ($googleCalendar === null) {
            return response('Usuario eliminado correctamente de la dependencia. No se encontraron calendarios asociados', 200);
        }

        $googleCalendarApi = new GoogleCalendarApi($googleCalendar->google_calendar_id);
        $googleCalendar->delete();
        try {
            $googleCalendarApi->deleteCalendar();
        } catch (\JsonException $e) {
            return response('Usuario eliminado correctamente de la dependencia. Todos los calendarios asociados
        fueron eliminados también', 200);
        } catch (GoogleCalendarApiException $e) {
            return response('Usuario eliminado correctamente de la dependencia.
            Sin embargo, hubo un error al borrar su calendario de Google', 200);
        }
        return response('Usuario eliminado correctamente de la dependencia. :)', 200);


    }
}
