<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
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


    }
}
