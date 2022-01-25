<?php

namespace App\Http\Controllers;

use App\Models\GoogleCalendar;
use App\Http\Requests\StoreGoogleCalendarRequest;
use App\Http\Requests\UpdateGoogleCalendarRequest;

class GoogleCalendarController extends Controller
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
     * @param  \App\Http\Requests\StoreGoogleCalendarRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGoogleCalendarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GoogleCalendar  $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function show(GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GoogleCalendar  $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function edit(GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGoogleCalendarRequest  $request
     * @param  \App\Models\GoogleCalendar  $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGoogleCalendarRequest $request, GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GoogleCalendar  $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleCalendar $googleCalendar)
    {
        //
    }
}
