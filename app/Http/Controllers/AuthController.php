<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController As Feedback;
use App\Http\Controllers\SettingController As Settings;
use Illuminate\Support\Facades\Input;

class AuthController extends Controller
{

//    private static function isTokenAlive($timeOfLastVisit)
//    {
//        return (Settings::take('TOKEN_LIFE_TIME') > (time() - $timeOfLastVisit->timestamp));
//    }

    public static function id(Request $request) {
        $token = $request->input('access_token');
        $user = User::where('access_token', $token)->first();
        return $user->id;
    }

    public static function getSurnameAndNameOfUserById($id) {
        $user = User::find($id);
        return $user->surname . ' ' . $user->name;
    }



//    public function login(Request $request)
//    {
//        $email = $request->input('email');
//        $password = $request->input('password');
//
//        $user = ApiUser::where('email', $email)->where('password', $password)->first();
//
//        if ($user && $user->active == true) {
//
//            $token = bin2hex(random_bytes(30));
//            $user->access_token = $token;
//            $user->save();
//
//            return Feedback::getFeedback(0, [
//                'access_token' => $user->access_token,
//                'name' => $user->name,
//                'surname' => $user->surname,
//                'role' => $user->role,
//                'email' => $user->email,
//                'id' => $user->id,
//                'isDefaultPassword' =>
//                    (hash('sha256', Settings::take('DEFAULT_PASSWORD')) === $user->password)
//            ]);
//
//        } else  return Feedback::getFeedback(101);
//
//    }
//
//
//    public function loginByToken(Request $request)
//    {
//        $token = $request->input('access_token');
//        $user = ApiUser::where('access_token', $token)->first();
//
//        if ($user && $user->active == true && $token != "") {
//
//            if (!self::isTokenAlive($user->updated_at)) return Feedback::getFeedback(102);
//
//            return Feedback::getFeedback(0, [
//                'access_token' => $user->access_token,
//                'name' => $user->name,
//                'surname' => $user->surname,
//                'role' => $user->role,
//                'email' => $user->email,
//                'id' => $user->id,
//                'isDefaultPassword' =>
//                    (hash('sha256', Settings::take('DEFAULT_PASSWORD')) === $user->password)
//            ]);
//
//        } else  return Feedback::getFeedback(102);
//
//    }

    public static function isTokenValid(Request $request)
    {
        $token = $request->input('access_token');
        $user = ApiUser::where('access_token', $token)->first();

        if ($user && $token != "") {

            if (!self::isTokenAlive($user->updated_at)) return Feedback::getFeedback(102);

            return Feedback::getFeedback(0);
        }

        return Feedback::getFeedback(103);

    }


}
