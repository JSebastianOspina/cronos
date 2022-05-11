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

}
