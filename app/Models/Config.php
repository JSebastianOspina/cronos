<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Config extends Model
{
    use HasFactory;

    public static function getGoogleAccessToken()
    {

        $config = DB::table('configs')->where('key', '=', 'google_access_token')->first();
        if ($config === null) {
            return null;
        }
        return $config->value;
    }

    public static function getGoogleRefreshToken()
    {

        $config = DB::table('configs')->where('key', '=', 'google_refresh_token')->first();
        if ($config === null) {
            return null;
        }
        return $config->value;
    }

    public static function getGoogleClientId()
    {

        $config = DB::table('configs')->where('key', '=', 'google_client_id')->first();
        if ($config === null) {
            return null;
        }
        return $config->value;
    }


    public static function getGoogleClientSecret()
    {

        $config = DB::table('configs')->where('key', '=', 'google_client_secret')->first();
        if ($config === null) {
            return null;
        }
        return $config->value;
    }
}
