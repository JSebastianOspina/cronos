<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            'key' => 'google_client_id',
            'value' => '202224303067-tlcghnil25ebniqojdcbpn4qduqtg5uj.apps.googleusercontent.com'
        ]);
        DB::table('configs')->insert([
            'key' => 'google_client_secret',
            'value' => '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'
        ]);
        DB::table('configs')->insert([
            'key' => 'google_access_token',
            'value' => 'ya29.A0ARrdaM-gS-MOpM1RiE7Mxb5sWiDo18Nxw094e4GNpVpEj-LzLRMJE0H2YHqQAkSUvVGUYSt3QFQoBO1MVW5EHEACu_eebHy3YNVWvzMElYoQ8M5UcdOyvNDbbLct9HPylmOlUpFuPJJTutwKb0GrrJdfA35DPw'
        ]);
        DB::table('configs')->insert([
            'key' => 'google_refresh_token',
            'value' => '1//055vtpJWb_B1_CgYIARAAGAUSNwF-L9IrcT23XTbF7JJbTq4ybX7tfHdew5huOsVfeu4PjqNDQv6qmjj1HEuya7AiGW2v8nj0Hto'
        ]);
    }
}
