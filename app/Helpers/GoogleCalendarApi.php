<?php

namespace App\Helpers;

class GoogleCalendarApi
{
    private $token;
    private $calendarId;

    /**
     * @param $token
     */
    public function __construct($token, $calendarId)
    {
        $this->token = $token;
        $this->calendarId = $calendarId;
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
