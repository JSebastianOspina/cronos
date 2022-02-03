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
            return response('Horario de monitor borrado exitosamente, pero no se pudo encontrar ningun calendario de Google Asociado', 200);
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


    public function userSchedules(User $user, $dependencyId, Request $request)
    {
        $schedules = Schedule::where('monitor_id', $user->id)
            ->where('dependency_id', $dependencyId)
            ->get();

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

        //Verify if the monitor doesn't have a schedule in the specific time range already
        $userSchedules = \DB::table('schedules')->where('monitor_id', '=', $userId)->get();
        foreach ($userSchedules as $schedule) {


            //Create carbon instace of desired hours
            $startDate = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('start_hour'));
            $endDate = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('end_hour'));

            //schedule hours as carbon instance
            $userScheduleStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date . ' ' . $schedule->start_hour);
            $userScheduleEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date . ' ' . $schedule->end_hour);

            //Check if the desired hours are between that range

            if ($startDate->isBetween($userScheduleStartDate, $userScheduleEndDate) === true) {
                $dependency = \DB::table('dependencies')->where('id', $schedule->dependency_id)
                    ->first();

                if ($dependency === null) {
                    return response('Ha ocurrido un error, por favor intentalo mas tarde', 500);
                }

                $errorMessage = 'No se ha podido crear el evento. El monitor tiene un horario activo comprendido entre
                las ' . $userScheduleStartDate->toTimeString() . ' y las ' . $userScheduleEndDate->toTimeString() . ' en la dependencia
                 ' . $dependency->name;
                return response($errorMessage, 403);
            }

            if ($endDate->isBetween($userScheduleStartDate, $userScheduleEndDate) === true) {

                $dependency = \DB::table('dependencies')->where('id', $schedule->dependency_id)
                    ->first();
                if ($dependency === null) {
                    return response('Ha ocurrido un error, por favor intentalo mas tarde', 500);
                }
                $errorMessage = 'No se ha podido crear el evento. El monitor tiene un horario activo comprendido entre
                las ' . $userScheduleStartDate->toTimeString() . ' y las ' . $userScheduleEndDate->toTimeString() . ' en la dependencia
                 ' . $dependency->name;
                return response($errorMessage, 403);
            }

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
            $googleCalendarEvent = $googleCalendarApi->createEvent($startDate, $endDate, $calendar->dependency->name, $calendar->user->email, $request->input('type') === 'periodic');
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

        //Finally, migrate the data to the records table IF IS UNIQUE

        if ($request->input('type') === 'unique') {
            Record::create([
                'dependency_id' => $dependencyId,
                'monitor_id' => $userId,
                'supervisor_id' => auth()->user()->id,
                'start_planned_date' => $startDate,
                'end_planned_date' => $endDate,
                'status' => 'created',
            ]);

            return response('El evento ha sido creado correctamente. También se ha creado el registro de monitoria (turno) para el día programado', 201);
        }

        return response('El evento PERIODICO ha sido creado correctamente. El registro será creado a la 1 AM del día programado y se repitirá este comportamiento todas las semanas', 201);
    }


}
