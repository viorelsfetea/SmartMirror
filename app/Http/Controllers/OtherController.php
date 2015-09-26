<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cache;
use Forecast;
use App\User;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Storage\TokenStorageInterface;

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
        $fallbackQuote = "I come from a stupid family. My father worked in a bank. They caught him stealing pens.";
        $fallbackAuthor = "Rodney Dangerfield";

        $url = "http://api.theysaidso.com/qod.json?maxlength=200&category=funny";

        try {
            $response = Cache::get('quote');

            if( !$response )
            {
                $apiResponse = json_decode(file_get_contents($url));

                $response = [
                  'quote' => $apiResponse->contents->quotes['0']->quote,
                  'author' => $apiResponse->contents->quotes['0']->author,
                ];

                Cache::put('quote', $response, 90);
            }
        } catch( \ErrorException $e ) {
            $response = [
                'quote' => $fallbackQuote,
                'author' => $fallbackAuthor
            ];
        }

        return $response;
    }

    public function event(Request $request, $id)
    {
        $user = User::find($id);

        $cache_key = 'event' . $user->id;

        $response = Cache::get($cache_key);

        if( !$response )
        {
            if( !$user->google_access_token )
            {
                return [
                    'status' => 'no_token'
                ];
            }
            $googleService = \OAuth::consumer('Google');

            $storage = $googleService->getStorage();

            $token = new StdOAuth2Token();
            $token->setAccessToken($user->google_access_token);

            $storage->storeAccessToken('Google', $token);

            $resultRaw = json_decode($googleService->request('https://www.googleapis.com/calendar/v3/calendars/primary/events?orderBy=startTime&singleEvents=true&timeMin=' . urlencode(date("Y-m-d\TH:i:sP"))), true);

            $response = [
              'status' => 'ok',
              'name' => $resultRaw['items'][0]['summary'],
              'location' => $resultRaw['items'][0]['location'],
              'start' => date("F j, g:i a", strtotime($resultRaw['items'][0]['start']['dateTime'])),
              'end' => date("F j, g:i a", strtotime($resultRaw['items'][0]['end']['dateTime'])),
            ];

            Cache::put($cache_key, $response, 90);
        }

        return $response;

    }
}
