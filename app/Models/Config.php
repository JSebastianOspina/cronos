<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Config
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereValue($value)
 * @mixin \Eloquent
 */
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
