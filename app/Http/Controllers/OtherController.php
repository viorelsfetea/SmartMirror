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

    public function quote()
    {
        $fallback = "I come from a stupid family. My father worked in a bank. They caught him stealing pens.";
        $url = "http://api.theysaidso.com/qod.json?maxlength=200&category=funny";

        try {
            $response = Cache::get('quote');

            if( !$response )
            {
                $apiResponse = json_decode(file_get_contents($url));

                $response = $apiResponse->contents->quotes['0']->quote;

                Cache::put('quote', $response, 90);
            }
        } catch( \ErrorException $e ) {
            $response = $fallback;
        }

        return [
          'quote' => $response
        ];
    }
}
