<?php

namespace App\Http\Controllers;

use App\Helpers\CurlCobain;
use App\Helpers\GoogleCalendarApi;
use App\Models\Config;
use App\Models\GoogleCalendar;
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
        $schedule->delete();
        return response('Horario de monitor borrado exitosamente', 200);

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
            'day_of_week' => $dayOfWeek
        ]);

        // create carbon objets and ask google calendar to create the event

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('start_hour'));
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('end_hour'));
        return $startDate;

        $this->handleEventCreation($calendar, $startDate, $endDate);
        return response('', 201);
    }

    public function handleEventCreation($calendar, $startDate, $endDate)
    {
        $calendarId = 'c_hklcegv8n3vq4nibep6vplhb50@group.calendar.google.com';

        try {
            $googleCalendarApi = new GoogleCalendarApi($calendarId);
        } catch (\RuntimeException $e) {
            return response('Ha ocurrido el siguiente error: ' . $e->getMessage(), 500);
        }
        $request = $googleCalendarApi->createEvent('2022-01-26T17:55:00-05:00', '2022-01-26T18:00:00-05:00', 'Biblioteca', '2420171030@estudiantesunibague.edu.co');
        $requestObject = json_decode($request, true);

        if (isset($requestObject['error']) && $requestObject['error']['code'] === 401) {
            //The token has expired, let's reauthorize and save a new token.
            GoogleCalendarApi::updateAccessToken();
            Log::info('Se ha actualizado el token, el: ' . Carbon::now()->toDateTimeString());
            //Repeat the event creation
            $this->handleEventCreation($calendar, $startDate, $endDate);
            die;
        }
        dd($requestObject);

    }
}
