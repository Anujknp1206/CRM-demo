<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;


class LocationController extends Controller
{
    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        return response()->json($cities);
    }
    // CountryController
    public function storeCountry(Request $request)
    {
        $country = Country::create($request->all());
        return response()->json($country);
    }

    // StateController
    public function storeState(Request $request)
    {
        $state = State::create($request->all());
        return response()->json($state);
    }

    // CityController
    public function storeCity(Request $request)
    {
        $city = City::create($request->all());
        return response()->json($city);
    }

}
