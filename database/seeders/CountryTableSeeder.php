<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $url = base_path('database/seeders/json/countries.json');

        if (!file_exists($url)) {
            throw new \Exception("File not found at path: $url");
        }

        $countriesJson = json_decode(file_get_contents($url), false);

        $countries = array_map(function ($country){

            return [
                'name'          => json_encode(['ar' => $country->native, 'en' => $country->name], JSON_UNESCAPED_UNICODE),
                'key'           => $country->phone_code,
                'flag'          => $country->emoji,
                'iso2'          => $country->iso2,
                'currency_code' => $country->currency_symbol,
                'currency'      => json_encode(['ar' => $country->currency, 'en' => $country->currency], JSON_UNESCAPED_UNICODE),
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ];
        }, $countriesJson);

        DB::table('countries')->delete();

        DB::table('countries')->insert($countries);
    }

}
