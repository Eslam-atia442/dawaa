<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $url = base_path('database/seeders/json/regions.json');

        if (!file_exists($url)) {
            throw new \Exception("File not found at path: $url");
        }

        $regionsJson = json_decode(file_get_contents($url), false);

        $regions = array_map(function ($region){
            return [
                'name'       => json_encode(['ar' => $region->name_ar, 'en' => $region->name_en], JSON_UNESCAPED_UNICODE),
                'country_id' => $region->country_id,
                'created_at' => \Carbon\Carbon::now()->subMonth(rand(0, 8)),
            ];
        }, $regionsJson);

        DB::table('regions')->insert($regions);
    }

}
