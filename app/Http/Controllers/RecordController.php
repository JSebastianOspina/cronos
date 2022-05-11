<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Record;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 *
 */
class RecordController extends Controller
{
    /**
     * @param $dependencyId
     * @return \Inertia\Response|string
     */
    public function filterDependencyRecordsView($dependencyId)
    {
        $dependency = Dependency::find($dependencyId);
        if ($dependency === null) {
            return 'La dependencia seleccionada no existe...';
        }

        return Inertia::render('records/DependencyRecords', [
            'dependency' => $dependency
        ]);

    }

    /**
     * @param $dependencyId
     * @param Request $request
     * @return JsonResponse
     */
    public function filterDependencyRecords($dependencyId, Request $request): JsonResponse
    {

        $records = Record::with('monitor')->where('dependency_id', '=', $dependencyId)
            ->orderBy('start_planned_date', 'asc')
            ->where('start_planned_date', '>=', $request->input('startDate'))
            ->where('start_planned_date', '<=', $request->input('endDate'))
            ->get();

        if (count($records) === 0) {
            return response()->json(['error' => 'No hay registros para este rango horario'], 404);

        }

        return response()->json(['records' => $records], 200);
    }


    /**
     * @param Request $request
     * @param $dependencyId
     * @param $userId
     * @return Response
     */
    public function downloadUserDependencyRecords(Request $request, $dependencyId, $userId): Response
    {

        //If is not supervisor and the user id doesn't match, show error
        if (!(auth()->user()->isSupervisor()) && ($userId != auth()->user()->id)) {
            return response('No tienes permisos para realizar esta acción', 401);
        }

        $records = DB::table('records')
            ->select([
                'start_planned_date',
                'end_planned_date',
                'start_monitor_date',
                'end_monitor_date',
                'start_approved_date',
                'end_approved_date',
            ])
            ->where('dependency_id', '=', $dependencyId)
            ->where('start_planned_date', '>=', $request->input('startDate'))
            ->where('start_planned_date', '<=', $request->input('endDate'))
            ->where('monitor_id', '=', $userId)
            ->orderBy('start_planned_date', 'asc')
            ->get();
        
        //Verify if has values, if not, return error.
        if (count($records) === 0) {
            return response('No existen registros para el usuario y rango de fechas seleccionados', 404);
        }

        //Get monitor
        $monitor = User::find($userId);
        $userName = $monitor->name;

        //Get dependency
        $dependencyName = Dependency::find($dependencyId)->name;

        //Count total worked minutes
        $totalMinutes = 0;
        foreach ($records as $record) {
            //Verify that both times are presented.
            if ($record->start_approved_date === null || $record->end_approved_date === null) {
                //Abort execution for this cycle
                continue;
            }
            //Count minutes.
            //Create carbon objects
            $startHour = new Carbon($record->start_approved_date);
            $endHour = new Carbon($record->end_approved_date);

            $passedMinutes = $startHour->diffInMinutes($endHour);
            $totalMinutes += $passedMinutes;
        }
        $totalHours = $totalMinutes / 60;
        $minutes = $totalMinutes % 60;
        //Table headers
        $header = ['Hora de Inicio', 'Hora de salida', 'Check In', 'Check Out', ' Hora de inicio (Supervisor)', 'Hora de salida (supervisor)'];

        return Pdf::loadView('reports.generate', compact('records', 'userName', 'header', 'totalHours', 'dependencyName', 'minutes'))
            ->stream('Reporte cronos.pdf');

    }

    /**
     * @param $recordId
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSupervisorHour($recordId, Request $request): JsonResponse
    {
        //Prepare variables
        $hour = $request->input('hour');
        $type = $request->input('type');

        //Find record
        $record = Record::findOrFail($recordId);

        $date = Carbon::parse($record->start_planned_date);
        $date->setTimeFromTimeString($hour);

        //Assign to start or end approved date
        if ($type === 'start') {
            $record->start_approved_date = $date->toDateTimeString();
        }
        if ($type === 'end') {
            $record->end_approved_date = $date->toDateTimeString();
        }

        //Check if supervisor register both hours
        if ($record->end_approved_date !== null && $record->start_approved_date !== null) {
            $record->status = 'approved';
        } else if ($record->end_monitor_date !== null && $record->start_monitor_date !== null) { //Check if user already check in and check out
            $record->status = 'finished';
        } else if ($record->start_monitor_date !== null) { //the user already do check in
            $record->status = 'in_process';
        }

        //Save everything
        $record->save();
        return response()->json(['msg' => 'Se ha actualizado correctamente la hora del registro'], 200);

    }

    /**
     * @param $recordId
     * @param Request $request
     * @return JsonResponse
     */
    public function makeObservation($recordId, Request $request)
    {
        $request->validate([
            'observation' => 'required|string'
        ]);

        $record = Record::find($recordId);
        $this->updateRecordObservation($record, $request->input('observation'), 'Proporcionado en comentario');
        $record->save();
        return response()->json(['msg' => 'La observación ha sido creada exitosamente'], 200);

    }

    /**
     * @param $recordId
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelMonitorHours($recordId, Request $request): JsonResponse
    {
        $request->validate([
            'observation' => 'required|string'
        ]);

        $record = Record::find($recordId);
        $record->status = 'canceled';
        $record->start_approved_date = null;
        $record->end_approved_date = null;
        $this->updateRecordObservation($record, $request->input('observation'), 'canceled');

        $record->save();
        return response()->json(['msg' => 'Se han cancelado las horas del monitor. Se recargará la página'], 200);

    }

    /**
     * @param $record
     * @param $message
     * @param $status
     * @return void
     */
    private function updateRecordObservation(&$record, $message, $status)
    {
        //Generate observation model
        $observation = [
            'date' => Carbon::now()->toDateTimeString(),
            'message' => $message,
            'supervisor' => auth()->user()->name,
            'status' => $status
        ];
        if ($record->observation === null) {
            $record->observation = json_encode([$observation]);
        } else {
            $actualObservation = json_decode($record->observation, true);
            $actualObservation[] = $observation;
            $record->observation = json_encode($actualObservation);
        }
    }

    /**
     * @return \Inertia\Response
     */
    public function showCheckInOutView(): \Inertia\Response
    {
        return Inertia::render('checks/InOut');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getActiveRecords(Request $request)
    {
        $records = Record::getUserActiveRecords();
        if (count($records) === 0) {
            return response()->json([
                'error' => 'No tienes monitorias activas en este momento. Si tienes una monitoría programada, por favor espera a la hora de inicio'
            ], 404);
        }
        return response()->json($records);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function makeCheckInOrCheckout(Request $request): JsonResponse
    {
        $recordId = $request->input('recordId');
        $record = Record::find($recordId);
        if ($record === null) {
            return response()
                ->json(['error' =>
                    'No fue posible realizar la acción'
                ], 404);
        }
        $isCheckIn = false;
        if ($record->status === 'created' && $record->start_monitor_date === null) {
            $record->start_monitor_date = Carbon::now()->toDateTimeString();
            $record->status = 'in_process';
            $record->save();
            return response()->json(['msg' => 'Check in realizado exitosamente'], 200);
        }

        if ($record->status === 'in_process' && $record->start_monitor_date !== null) {
            $record->end_monitor_date = Carbon::now()->toDateTimeString();
            $record->status = 'finished';
            $record->save();
            return response()->json(['msg' => 'Check out realizado exitosamente'], 200);
        }

        return response()->json(['msg' => 'Check out realizado exitosamente'], 200);

    }

    /**
     * @return string
     */
    public static function createPeriodicRecords(): string
    {
        $now = Carbon::now();

        $schedules = \DB::table('schedules')
            ->where('type', '=', 'periodic')
            ->where('day_of_week', '=', $now->dayOfWeek)
            ->get();

        $counter = 0;
        foreach ($schedules as $schedule) {

            $startDate = Carbon::today();
            $startDate->setTimeFromTimeString($schedule->start_hour);

            $endDate = Carbon::today();
            $endDate->setTimeFromTimeString($schedule->end_hour);

            Record::create([
                'dependency_id' => $schedule->dependency_id,
                'monitor_id' => $schedule->monitor_id,
                'supervisor_id' => $schedule->supervisor_id,
                'start_planned_date' => $startDate->toDateTimeString(),
                'end_planned_date' => $endDate->toDateTimeString(),
                'status' => 'created',
            ]);
            $counter++;
        }

        Log::info('Se han creado ' . $counter . ' records. Proceso realizado el ' . Carbon::now()->toDateTimeString());
        return 'Se han creado ' . $counter . ' records. Proceso realizado el ' . Carbon::now()->toDateTimeString();
    }
}
