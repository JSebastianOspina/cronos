<?php

namespace App\Http\Controllers;

use App\Helpers\CurlCobain;
use App\Helpers\GoogleCalendarApi;
use App\Helpers\GoogleCalendarApiException;
use App\Models\Config;
use App\Models\GoogleCalendar;
use App\Models\Record;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ScheduleController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        $googleCalendar = GoogleCalendar::where('user_id', '=', $schedule->monitor_id)
            ->where('dependency_id', '=', $schedule->dependency_id)
            ->first();
        //Validate if not exists
        if ($googleCalendar === null) {
            $schedule->delete();
            return response('Horario de monitor borrado exitosamente, pero no se pudo eliminar el evento
            de Google Calendar', 200);
        }

        try {
            $googleCalendarApi = new GoogleCalendarApi($googleCalendar->google_calendar_id);
            $googleCalendarApi->deleteEvent($schedule->google_event_id);

        } catch (GoogleCalendarApiException $e) {
            $schedule->delete();
            return response('Horario de monitor borrado exitosamente. Sin embargo, no se pudo eliminar el evento
            de Google Calendar debido a: ' . $e->getMessage(), 200);
        } catch (\Exception $e) {
            $schedule->delete();
            return response('Horario de monitor borrado exitosamente, error JSON ' . $e->getMessage(), 200);
        }

        $schedule->delete();
        return response('Horario de monitor borrado exitosamente del sistema y de Google Calendar', 200);

    }


    public function userSchedules(User $user, Request $request)
    {
        $schedules = $user->schedules;

        return Inertia::render('schedules/UserSchedules', [
            'schedules' => $schedules,
            'user' => $user
        ]);

    }

    public function storeUserSchedule($userId, Request $request)
    {
        /*--------------VALIDATION SECTION  -------------------*/
        //Request validation
        $request->validate([
            'date' => 'required',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'type' => 'required'
        ]);
        //Permission validation
        $dependencyId = auth()->user()->getSupervisedDepencyId();
        if ($dependencyId === null) {
            return response('No puedes gestionar el horario de un usuario si no eres supervisor de la dependencia', 403);
        }
        // business logic validation (valid calendar)
        $calendar = GoogleCalendar::where('user_id', $userId)->where('dependency_id', $dependencyId)->first();
        if ($calendar === null) {
            return response('El usuario no tiene un calendario asociado. Por favor agregarlo nuevamente a la dependencia', 400);
        }
        /*--------------END VALIDATION SECTION  -------------------*/

        try {
            // create carbon objets and
            $startDate = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('start_hour'));
            $endDate = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('end_hour'));
            //ask google calendar to create the event
            $googleCalendarApi = new GoogleCalendarApi($calendar->google_calendar_id);
            $googleCalendarEvent = $googleCalendarApi->createEvent($startDate, $endDate, $calendar->dependency->name, $calendar->user->email);
        } catch (\RuntimeException $e) {
            return response('Ha ocurrido el siguiente error: ' . $e->getMessage(), 500);
        }

        //Create schedule in local database
        $dayOfWeek = Carbon::parse($request->input('date'))->dayOfWeek;
        Schedule::create([
            'start_hour' => $request->input('start_hour'),
            'end_hour' => $request->input('end_hour'),
            'type' => $request->input('type'),
            'date' => $request->input('date'),
            'monitor_id' => $userId,
            'supervisor_id' => auth()->user()->id,
            'dependency_id' => $dependencyId,
            'day_of_week' => $dayOfWeek,
            'google_event_id' => $googleCalendarEvent['id']
        ]);

        //Finally, migrate the data to the records table

        Record::create([
            'dependency_id' => $dependencyId,
            'monitor_id' => $userId,
            'supervisor_id' => auth()->user()->id,
            'start_planned_date' => $startDate,
            'end_planned_date' => $endDate,
            'status' => 'created',
        ]);

        return response('', 201);
    }

    public function handleEventCreation($calendar, $startDate, $endDate): void
    {
        $googleCalendarApi = new GoogleCalendarApi($calendar->google_calendar_id);
        $googleCalendarApi->createEvent($startDate, $endDate, $calendar->dependency->name, $calendar->user->email);
    }
}
