<?php

namespace App\Helpers;

use App\Models\Config;

class GoogleCalendarApi
{
    private $token;
    private $calendarId;

    /**
     * @param $calendarId
     * @param null $token
     */
    public function __construct($calendarId, $token = null)
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

    public function updateAccessToken(): void
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

    }

    public function createEvent($startHour, $endHour, $dependencyName, $monitorEmail)
    {

        $calendarId = $this->calendarId;
        $url = "https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?sendNotifications=true&sendUpdates=all";
        $token = $this->token;

        $curlCobain = new CurlCobain($url, 'POST');
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

            'description' => 'Este evento ha sido generado automaticamente por cronos',
            'summary' => 'Monitoria de: ' . $dependencyName,
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
        return $curlCobain->makeRequest();
    }

}
