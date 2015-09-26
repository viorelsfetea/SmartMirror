<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\History;
use Mockery\CountValidator\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use QrCode;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    public function insertTest()
    {
        $user = new User;

        $user->name = 'Test Name' . rand(1, 10000);
        $user->latest_weight = rand(1, 105);
        $user->device_id = rand(1, 100005);

        $user->save();

        return ['status' => 'ok'];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_get(Request $request)
    {
        $deviceId = $request->input('device_id');
        $weight = $request->input('weight');
        $googleAccessToken = $request->session()->get('google_access_token');

        if( $deviceId && $weight )
        {
          $request->session()->put('deviceId', $deviceId);
          $request->session()->put('weight', $weight);
        } else {
          $deviceId = $request->session()->get('deviceId');
          $weight = $request->session()->get('weight');
        }

        return view('users/create', ['device_id' => $deviceId, 'weight' => $weight, 'googleAccessToken' => $googleAccessToken]);
    }

    public function create_post(Request $request)
    {
        $user = new User;

        $user->device_id = $request->input('device_id');
        $user->latest_weight = $request->input('weight');
        $user->name = $request->input('name');
        $user->google_access_token = $request->input('google_access_token');

        $request->session()->forget('google_access_token');

        $user->save();

        $this->saveHistory($user->id, $user->latest_weight);

        return view('users/account_created');

    }

    public function is_created(Request $request)
    {
      $deviceId = $request->input('device_id');
      $weight = $request->input('weight');

      $user = User::where('device_id', '=', $deviceId)
        ->where('latest_weight', '>=', $weight - 1)
        ->where('latest_weight', '<=', $weight + 1)
        ->first();

      if( $user )
      {
        return [
          'status' => 'ok',
          'id' => $user->id,
          'name' => $user->name
        ];
      }

      return [
        'status' => 'not_created'
      ];

    }

  /**
   * @param Request $request
   * @return array
   */
    public function check(Request $request)
    {
        $deviceId = $request->input('device_id');
        $weight = $request->input('weight');

        $user = User::where('device_id', '=', $deviceId)
                    ->where('latest_weight', '>=', $weight - 1)
                    ->where('latest_weight', '<=', $weight + 1)
                    ->first();

        if( $user )
        {
          return $this->checkOK($user, $weight);
        }

        return $this->checkFailed($weight, $deviceId);
    }

    /**
     * @param User $user
     * @param $weight
     * @return array
     */
    private function checkOK(User $user, $weight)
    {
      $this->saveHistory($user->id, $weight);

      $result = [
        'status' => 'ok',
        'id' => $user->id,
        'name' => $user->name,
        'latest_weight' => $user->latest_weight,
        'new_weight' => (int)$weight,
        'history' => $this->getHistory($user->id)
      ];

      $user->latest_weight = $weight;

      $user->save();

      return $result;
    }

    private function getHistory($id)
    {
      $items = History::where('user_id', '=', $id)->get();

      $result = [];

      foreach( $items AS $item )
      {
        $result[] = [
          'day' => strtotime($item->day),
          'weight' => $item->weight,
        ];
      }

      return $result;
    }

    private function saveHistory($id, $weight)
    {
      $history = App\History::where('user_id', '=', $id)->where('day', '=', date('Y-m-d'))->get();

      if( !count($history) )
      {
        $history = new History;
        $history->user_id = $id;
        $history->weight = $weight;
        $history->day = date('Y-m-d');
        $history->save();
      }
    }

    /**
     * @param $weight
     * @param $deviceId
     * @return array
     */
    private function checkFailed($weight, $deviceId)
    {

      $qrCodeFile = sprintf('/qrcodes/%s.png', $deviceId.$weight);
      $url = sprintf('%s/users/create?weight=%d&device_id=%s', url(), $weight, $deviceId);
      QrCode::format('png')->size(600)->generate($url, '../public' . $qrCodeFile);

      return [
        'status' => 'not_found',
        'qr_code' => url() . $qrCodeFile
      ];
    }

    public function login_google(Request $request)
    {
      // get data from request
      $code = $request->get('code');

      // get google service
      $googleService = \OAuth::consumer('Google');

      // check if code is valid

      // if code is provided get user data and sign in
      if ( ! is_null($code))
      {

        // This was a callback request from google, get the token
        $tokenData = $googleService->requestAccessToken($code);
        $accessToken = $tokenData->getAccessToken();

        $request->session()->put('google_access_token', $accessToken);
        return redirect()->action('UserController@create_get');

      }
      // if not ask for permission first
      else
      {
        // get googleService authorization
        $url = $googleService->getAuthorizationUri();

        // return to google login url
        return redirect((string)$url);
      }
    }

    public function disconnect_google(Request $request)
    {
      $request->session()->forget('google_access_token');

      return redirect()->action('UserController@create_get');
    }
    /**
     * Get all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $user = new User;

        return $user::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user = User::find($id);

      return view('users/create', ['id' => $user->id, 'name' => $user->name, 'device_id' => $user->device_id, 'weight' => $user->latest_weight, 'googleAccessToken' => $user->google_access_token]);
    }

    public function request_edit(Request $request)
    {
      $id = $request->input('id');

      $user = User::find($id);

      if( !empty($user->id) )
      {

      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      //
    }
}
