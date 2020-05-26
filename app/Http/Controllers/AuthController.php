<?php

namespace App\Http\Controllers;

use App\Exceptions\User\Login\InvalidLoginPassword;
use App\Exceptions\User\Login\InvalidToken;
use App\Exceptions\User\Login\UserSwitchedOff;
use App\Exceptions\User\Login\DeadToken;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController As Feedback;
use App\Http\Controllers\SettingController As Settings;

class AuthController extends Controller
{

    public static function currentUsedId(string $token)
    {
        $user = User::where('access_token', $token)->first();
        throw_if(is_null($user), new InvalidToken());

        return $user->id;
    }

    private static function isTokenAlive($timeOfLastVisit)
    {
        return (Settings::take('TOKEN_LIFE_TIME') > (time() - $timeOfLastVisit->timestamp));
    }

    public static function id(Request $request)
    {
        $token = $request->input('access_token');
        $user = User::where('access_token', $token)->first();
        return $user->id;
    }

    public static function getSurnameAndNameOfUserById($id)
    {
        $user = User::find($id);
        return $user->surname . ' ' . $user->name;
    }

    public function test(Request $request) {

        $user = User::where('email', 'noskov_as@niik.ru')->where('password', '1234')->first();

        return Feedback::success([
            'user' => $user->name,
            'driver' => config('database.default'),
            'database' => config('database.connections.sqlite.database')
        ]);
    }


    public function login(Request $request)
    {

        $email = $request->input('email', '');
        $password = $request->input('password', '');

        $user = User::where('email', $email)->where('password', $password)->first();
        throw_if(is_null($user), new InvalidLoginPassword());
        throw_if(!$user->active, new UserSwitchedOff());

        $token = bin2hex(random_bytes(30));
        $user->access_token = $token;
        $user->save();

        return Feedback::success([
            'access_token' => $user->access_token,
            'name' => $user->name,
            'surname' => $user->surname,
            'role' => $user->role,
            'email' => $user->email,
            'id' => $user->id,
            'isDefaultPassword' =>
                (hash('sha256', Settings::take('DEFAULT_PASSWORD')) === $user->password)
        ]);


    }

    public function loginByToken(Request $request)
    {
        $token = $request->input('access_token', '');
        throw_if($token == '', new InvalidToken());

        $user = User::where('access_token', $token)->first();
        throw_if(is_null($user), new InvalidToken());
        throw_if(!$user->active, new UserSwitchedOff());
        throw_if(!self::isTokenAlive($user->updated_at), new DeadToken());

        return Feedback::success([
            'access_token' => $user->access_token,
            'name' => $user->name,
            'surname' => $user->surname,
            'role' => $user->role,
            'email' => $user->email,
            'id' => $user->id,
            'isDefaultPassword' =>
                (hash('sha256', Settings::take('DEFAULT_PASSWORD')) === $user->password)
        ]);

    }

    public static function isTokenValid(Request $request)
    {
        $token = $request->input('access_token', '');
        throw_if($token == '', new InvalidToken());

        $user = User::where('access_token', $token)->first();
        throw_if(is_null($user), new InvalidToken());
        throw_if(!$user->active, new UserSwitchedOff());
        throw_if(!self::isTokenAlive($user->updated_at), new DeadToken());

        return Feedback::success();

    }


}
