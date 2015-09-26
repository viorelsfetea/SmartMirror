<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
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
        QrCode::size(600)->generate($request->url(), '../public/qrcodes/qrcode.svg');
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

        return view('users/create', ['device_id' => $deviceId, 'weight' => $weight]);
    }

    public function create_post(Request $request)
    {
        $user = new User;

        $user->device_id = $request->input('device_id');
        $user->latest_weight = $request->input('weight');
        $user->name = $request->input('name');

        $user->save();

        return view('users/account_created');

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
      $result = [
        'status' => 'ok',
        'id' => $user->id,
        'latest_weight' => $user->latest_weight,
        'new_weight' => (int)$weight
      ];

      $user->latest_weight = $weight;

      $user->save();

      return $result;
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
        //
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
        //
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
