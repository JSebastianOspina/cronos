<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RecordController extends Controller
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function dailyDependencyRecords($dependencyId)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $records = Record::with('monitor')->where('dependency_id', '=', $dependencyId)
            ->orderBy('start_planned_date', 'asc')
            ->where('start_planned_date', '>=', $today)
            ->where('start_planned_date', '<=', $tomorrow)
            ->get();

        return Inertia::render('records/DailyDependencyRecords', [
            'records' => $records,
            'today' => $today->toDateString()
        ]);

    }

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

    public function filterDependencyRecords($dependencyId, Request $request)
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

    public function updateSupervisorHour($recordId, Request $request)
    {
        //Prepare variables
        $hour = $request->input('hour');
        $type = $request->input('type');
        $now = Carbon::today();
        $now->setTimeFromTimeString($hour);

        //Find record
        $record = Record::find($recordId);
        //Assign to start or end approved date
        if ($type === 'start') {
            $record->start_approved_date = $now->toDateTimeString();
        }
        if ($type === 'end') {
            $record->end_approved_date = $now->toDateTimeString();
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

    public function cancelMonitorHours($recordId, Request $request)
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

    public function showCheckInOutView()
    {
        return Inertia::render('checks/InOut');
    }

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

    public function makeCheckInOrCheckout(Request $request)
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
