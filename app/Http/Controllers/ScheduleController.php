<?php

namespace App\Http\Controllers;

use App\Helpers\CurlCobain;
use App\Helpers\GoogleCalendarApi;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $request->validate([
            'date' => 'required',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'type' => 'required'
        ]);
        $dayOfWeek = Carbon::parse($request->input('date'))->dayOfWeek;

        $schedule = Schedule::create([
            'start_hour' => $request->input('start_hour'),
            'end_hour' => $request->input('end_hour'),
            'type' => $request->input('type'),
            'date' => $request->input('date'),
            'monitor_id' => $userId,
            'supervisor_id' => auth()->user()->id,
            'dependency_id' => auth()->user()->getSupervisedDepencyId(),
            'day_of_week' => $dayOfWeek
        ]);

        return response('', 201);
    }

    public function createEvent()
    {

        $calendarId = 'c_hklcegv8n3vq4nibep6vplhb50@group.calendar.google.com';
        $url = "https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?sendNotifications=true&sendUpdates=all";
        $token = 'ya29.A0ARrdaM-lfU7X2jrTK9E_HYaYJiVa1dj-hvICMdSNECyxNBjJnMo1_lXfZfvX12G_JDnilD8U0fzxqmPvCfuyHODf9b4EQikfQ6KTtoFakSGdbMgcz3DYyFse7-hdIWpZmmm43XlNDBma2PM_IySgXpcyA-pC';


        $googleCalendarApi = new GoogleCalendarApi($token, $calendarId);
        $request = $googleCalendarApi->createEvent('2022-01-26T17:55:00-05:00', '2022-01-26T18:00:00-05:00', 'Biblioteca', '2420171030@estudiantesunibague.edu.co');
        $requestObject = json_decode($request, true);

        if (isset($requestObject['error'])) {
            if ($requestObject['error']['code'] === 401) {
                //The token has expired, let's reauthorize and save a new token.
                $curlCobain = new CurlCobain('https://oauth2.googleapis.com/token', 'POST');
                $curlCobain->setDataAsFormUrlEncoded([
                    'client_id' => '202224303067-tlcghnil25ebniqojdcbpn4qduqtg5uj.apps.googleusercontent.com',
                    'client_secret' => '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',
                    'grant_type' => 'refresh_token',
                    'refresh_token' => '1//055vtpJWb_B1_CgYIARAAGAUSNwF-L9IrcT23XTbF7JJbTq4ybX7tfHdew5huOsVfeu4PjqNDQv6qmjj1HEuya7AiGW2v8nj0Hto'
                ]);
                dd($curlCobain->makeRequest());
            }
        }

    }
}
