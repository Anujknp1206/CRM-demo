<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class CountryStateCitySeeder extends Seeder
{
    public function run()
    {
        // Fetch JSON directly from URL
        $url = "https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/refs/heads/master/json/countries%2Bstates%2Bcities.json";
        $response = Http::timeout(180)->withOptions([
            'curl' => [
                CURLOPT_FOLLOWLOCATION => true,
            ]
        ])->get($url);
        $countriesData = $response->json();

        foreach ($countriesData as $c) {
            // Insert Country
            $country = Country::create([
                'id' => $c['id'],
                'name' => $c['name'],
                'code' => $c['iso2'],       // your migration 'code'
                'phonecode' => $c['phonecode'],  // your migration 'phonecode'
            ]);
            // Insert States
            foreach ($c['states'] as $s) {

                $state = State::create([
                    'id' => $s['id'],
                    'country_id' => $country->id,
                    'name' => $s['name'],
                ]);
                // Insert Cities
                foreach ($s['cities'] as $ct) {

                    City::create([
                        'id' => $ct['id'],
                        'state_id' => $state->id,
                        'name' => $ct['name'],
                    ]);
                }
            }
        }
        echo "Import completed successfully!";
    }
}
