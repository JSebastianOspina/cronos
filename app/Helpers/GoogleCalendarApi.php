<?php

namespace App\Helpers;

use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarApi
{
    private $token;
    private $calendarId;

    /**
     * @param $calendarId
     * @param null $token
     */
    public function __construct($calendarId = null, $token = null)
    {
        if ($token === null) {
            $token = Config::getGoogleAccessToken();
            if ($token === null) {
                throw new \RuntimeException('No Existe un token en la configuración de la aplicacion');
            }
        }
        $this->token = $token;
        $this->calendarId = $calendarId;
    }

    public static function exchangeAuthorizationCodeForRefreshAndAccessToken($authorizationCode)
    {
        $curlCobain = new CurlCobain('https://oauth2.googleapis.com/token', 'POST');
        $data = [
            'client_id' => Config::getGoogleClientId(),
            'client_secret' => Config::getGoogleClientSecret(),
            'code' => $authorizationCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => url('authorize/callback')
        ];
        $curlCobain->setDataAsFormUrlEncoded($data);
        return json_decode($curlCobain->makeRequest(), true);
    }

    /**
     * @throws \JsonException
     * @throws GoogleCalendarApiException
     */
    public function deleteCalendar()
    {

        $curlCobain = new CurlCobain('https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId, 'DELETE');
        //Set authentication
        $token = $this->token;
        $curlCobain->setHeader('Authorization', "Bearer ${token}");
        $request = $curlCobain->makeRequest();

        $requestAsObject = json_decode($request, true, 512, JSON_THROW_ON_ERROR);

        //Verify if was not successful
        if (!$this->requestWasAuthenticated($requestAsObject)) {
            //Exchange new token
            self::refreshAccessToken();
            //Update class instantiated token
            $this->updateAccessToken();
            //try again
            return $this->deleteCalendar();
        }
        if ($this->requestHasError($request)) {
            throw new GoogleCalendarApiException('Ha ocurrido un error con la api de google');
        }

        return $requestAsObject;
    }

    private function requestWasAuthenticated($request)
    {
        if (isset($request['error']) && $request['error']['code'] === 401) {
            return false;
        }
        return true;
    }

    public static function refreshAccessToken(): void
    {

        $curlCobain = new CurlCobain('https://oauth2.googleapis.com/token', 'POST');
        $curlCobain->setDataAsFormUrlEncoded([
            'client_id' => Config::getGoogleClientId(),
            'client_secret' => Config::getGoogleClientSecret(),
            'grant_type' => 'refresh_token',
            'refresh_token' => Config::getGoogleRefreshToken()
        ]);

        $refreshTokenRequest = $curlCobain->makeRequest();
        $refreshTokenRequestObject = json_decode($refreshTokenRequest, true);

        //Update access token on DB
        Config::updateOrCreate(
            ['key' => 'google_access_token'],
            [
                'value' => $refreshTokenRequestObject['access_token']
            ]
        );
        Log::info('Se ha actualizado el token, el: ' . Carbon::now()->toDateTimeString());
    }

    private function updateAccessToken(): void
    {
        $this->token = Config::getGoogleAccessToken();
    }

    private function requestHasError($request)
    {
        if (isset($request['error'])) {
            return true;
        }
        return false;
    }

    /**
     * @param $calendarName
     * @return array
     * @throws \JsonException
     */
    public function createCalendar($calendarName): array
    {
        $curlCobain = new CurlCobain('https://www.googleapis.com/calendar/v3/calendars', 'POST');
        //Set authentication
        $token = $this->token;
        $curlCobain->setHeader('Authorization', "Bearer ${token}");

        //Set body params
        $data = [
            'summary' => 'Horario de ' . $calendarName,
            'description' => 'Este horario ha sido generado automáticamente por Cronos',
            'timezone' => 'America/Bogota'
        ];
        $curlCobain->setDataAsJson($data);
        //Make request
        $request = $curlCobain->makeRequest();
        $requestAsObject = json_decode($request, true, 512, JSON_THROW_ON_ERROR);

        //Verify if was not successful
        if (!$this->requestWasAuthenticated($requestAsObject)) {
            return $this->UpdateTokenAndRetryRequest('createCalendar', $calendarName);
        }
        return $requestAsObject;
    }

    private function UpdateTokenAndRetryRequest($method, ...$params)
    {
        //Exchange new token
        self::refreshAccessToken();
        //Update class instantiated token
        $this->updateAccessToken();
        //try again
        return $this->$method(...$params);
    }

    public function inviteUserToCalendar($calendarId, $emailAddress): array
    {
        $endpoint = "https://www.googleapis.com/calendar/v3/calendars/${calendarId}/acl";
        $curlCobain = new CurlCobain($endpoint, 'POST');
        //Set authentication
        $token = $this->token;
        $curlCobain->setHeader('Authorization', "Bearer ${token}");

        //Set body params
        $data = [
            'role' => 'reader',
            'scope' => [
                'type' => 'default'
                //'value' => $emailAddress
            ]
        ];
        $curlCobain->setDataAsJson($data);
        //Make request
        $request = $curlCobain->makeRequest();
        $requestAsObject = json_decode($request, true, 512, JSON_THROW_ON_ERROR);

        //Verify if was not successful
        if (!$this->requestWasAuthenticated($requestAsObject)) {
            return $this->UpdateTokenAndRetryRequest('inviteUserToCalendar', $calendarId, $emailAddress);
        }
        return $requestAsObject;
    }

    public function createEvent($startHour, $endHour, $dependencyName, $monitorEmail)
    {

        $calendarId = $this->calendarId;
        $url = "https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?sendNotifications=true&sendUpdates=all";

        $curlCobain = new CurlCobain($url, 'POST');
        $token = $this->token;
        $curlCobain->setHeader('Authorization', "Bearer ${token}");
        $data = [
            'start' => [
                'dateTime' => $startHour,
                "timeZone" => 'America/Bogota',
            ],
            'end' => [
                'dateTime' => $endHour,
                "timeZone" => 'America/Bogota',
            ],

            'description' => 'Este evento ha sido generado automaticamente por Cronos',
            'summary' => 'Monitoria de ' . $dependencyName,
            'reminders' => [
                'overrides' => [
                    [
                        'method' => 'email',
                        'minutes' => 10
                    ]
                ],
                'useDefault' => false
            ],
            'attendees' => [
                [
                    'email' => $monitorEmail
                ]
            ]
        ];
        $curlCobain->setDataAsJson($data);
        $createEventRequest = $curlCobain->makeRequest();
        $createEventRequestObject = json_decode($createEventRequest, true);
        if (!$this->requestWasAuthenticated($createEventRequestObject)) {
            return $this->UpdateTokenAndRetryRequest('createEvent', $startHour, $endHour, $dependencyName, $monitorEmail);
        }
        return $createEventRequestObject;

    }

    /**
     * @throws GoogleCalendarApiException
     */
    public function deleteEvent($eventId)
    {
        if ($eventId === null) {
            throw new GoogleCalendarApiException('No se proporcionó un ID de evento');
        }

        $curlCobain = new CurlCobain('https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events/' . $eventId . '?sendUpdates=all', 'DELETE');
        //Set authentication
        $token = $this->token;
        $curlCobain->setHeader('Authorization', "Bearer ${token}");
        $request = $curlCobain->makeRequest();

        $requestAsObject = json_decode($request, true);

        //Verify if was not successful
        if (!$this->requestWasAuthenticated($requestAsObject)) {
            return $this->UpdateTokenAndRetryRequest('deleteEvent', $eventId);
        }
        if ($this->requestHasError($request)) {
            throw new GoogleCalendarApiException('Ha ocurrido un error con la api de google');
        }

        return $requestAsObject;


    }


}
