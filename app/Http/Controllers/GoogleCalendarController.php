<?php

namespace App\Http\Controllers;

use App\Helpers\CurlCobain;
use App\Helpers\GoogleCalendarApi;
use App\Models\Config;
use App\Models\GoogleCalendar;
use App\Http\Requests\StoreGoogleCalendarRequest;
use App\Http\Requests\UpdateGoogleCalendarRequest;
use Illuminate\Http\Request;

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

    public function generateAuthenticateUrl()
    {

        $clientId = Config::getGoogleClientId();
        $redirectUri = url('authorize/callback');
        $responseType = 'code';
        $scope = 'https://www.googleapis.com/auth/calendar';
        $accessType = 'offline';
        $baseUrl = 'https://accounts.google.com/o/oauth2/v2/auth';

        $encodeUrl = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
            'access_type' => $accessType,
            'prompt' => 'consent'
        ]);


        return redirect($baseUrl . '?' . $encodeUrl);
    }

    public function handleGoogleCallback(Request $request)
    {

        $googleResponse = GoogleCalendarApi::exchangeAuthorizationCodeForRefreshAndAccessToken($request->input('code'));
        Config::UpdateOrCreate(
            [
                'key' => 'google_access_token'
            ],
            [
                'value' => $googleResponse['access_token']
            ]
        );

        Config::UpdateOrCreate(
            [
                'key' => 'google_refresh_token'
            ],
            [
                'value' => $googleResponse['refresh_token']
            ]
        );

        return 'Aplicacion autorizada exitosamente';

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
     * @param \App\Http\Requests\StoreGoogleCalendarRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGoogleCalendarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\GoogleCalendar $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function show(GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\GoogleCalendar $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function edit(GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateGoogleCalendarRequest $request
     * @param \App\Models\GoogleCalendar $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGoogleCalendarRequest $request, GoogleCalendar $googleCalendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\GoogleCalendar $googleCalendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleCalendar $googleCalendar)
    {
        //
    }
}
