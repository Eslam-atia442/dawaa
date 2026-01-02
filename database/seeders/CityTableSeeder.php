<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $remote = isset($_SERVER["REMOTE_ADDR"]) ?? false;
        $url = base_path('database/seeders/json/cities.json');

        $regionsJson = json_decode(file_get_contents($url), false);

        $cities = array_map(function ($city) {
            return [
                'name'          =>  json_encode(['ar' => $city->name_ar , 'en' => $city->name_en ] , JSON_UNESCAPED_UNICODE),
                'country_id'    =>  $city->country_id,
                'region_id'     =>  $city->region_id,
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,8)),
            ];
        }, $regionsJson );

        DB::table('cities')->insert($cities) ;

    }
}
