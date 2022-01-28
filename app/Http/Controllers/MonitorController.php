<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\GoogleCalendar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = auth()->user();
        $dependencyId = $user->getSupervisedDepencyId();

        if ($dependencyId === null) {
            return 'No administras ninguna dependencia';
        }

        $dependency = Dependency::find($dependencyId);
        $monitors = $dependency->getMonitors();
        return Inertia::render('monitors/Index', [
            'monitors' => $monitors,
        ]);
    }

    public function showUserCalendars($monitor)
    {
        return Inertia::render('monitors/ShowUserCalendars', [
            'monitorId' => $monitor
        ]);
    }

    public function getUserCalendars($monitorId)
    {
        $calendars = GoogleCalendar::where('user_id', '=', $monitorId)->with('dependency')->get();
        return response()->json($calendars);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }
}
