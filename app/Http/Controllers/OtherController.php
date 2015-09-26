<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cache;
use Forecast;

class OtherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function weather()
    {
        $weather = Cache::get('weather');

        if( !$weather )
        {
            $rawWeather = Forecast::get('48.1549107', '11.5418357');

            $weather = [
                'temperature' => ceil($rawWeather['currently']['temperature']),
                'summary' => $rawWeather['currently']['summary'],
                'icon' => $rawWeather['currently']['icon'],
            ];

            Cache::put('weather', $weather, 30);
        }

        return $weather;
    }
}
