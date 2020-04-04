<?php

namespace App\Http\Controllers;

use App\Exceptions\User\Validation\Active;
use App\Exceptions\User\Validation\Name;
use App\Exceptions\User\Validation\Surname;
use App\Exceptions\User\Validation\Role;
use App\Exceptions\User\Validation\PermissionExpression;
use App\Exceptions\User\Validation\Email;

use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController As Feedback;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
//    public static function getUserId(Request $request) {
//        $token = $request->input('access_token');
//        $user = ApiUser::where('access_token', $token)->first();
//        return $user->id;
//    }


    public function validateActive($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
            new Active()
        );

        return true;
    }

    public function validateName($value)
    {
        throw_if(
            is_null($value) || !is_string($value),
            new Name()
        );

        return true;
    }

    public function validateSurname($value)
    {
        throw_if(
            is_null($value) || !is_string($value),
            new Surname()
        );

        return true;
    }

    public function validateRole($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '',
            new Role()
        );

        return true;
    }

    public function validateEmail($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '',
            new Email()
        );

        return true;
    }

    public function validatePermissionExpression($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '',
            new PermissionExpression()
        );

        return true;
    }


//    public function set(Request $request)
//    {
//        $validatedData = $request->validate([
//            'id' => 'required|integer|exists:users',
//            'active' => 'required|integer|min:0|max:1',
//            'name' => 'required|string|max:50',
//            'surname' => 'required|string|max:50',
//            'role' => 'required|string|exists:roles,name',
//            'email' => 'required|email:rfc',
//            'permission_expression' => 'required|string',
//        ]);
//


//    }

    public function create(Request $request)
    {
        $this->validateActive($request->input('active'));
        $this->validateName($request->input('name'));
        $this->validateSurname($request->input('surname'));
        $this->validateRole($request->input('role'));
        $this->validateEmail($request->input('email'));
        $this->validatePermissionExpression($request->input('permission_expression'));

        return Feedback::success([]);
    }

//    public function set1()
//    {
//        $id = Input::get('id', null);
//        $email = trim(Input::get('email', ''));
//        $surname = trim(Input::get('surname', ''));
//        $name = trim(Input::get('name', ''));
//        $role = trim(Input::get('role', ''));
//        $active = trim(Input::get('active', ''));
//        $permission_expression = trim(Input::get('permission_expression', ''));
//
//        if (!is_null($id)) {
//            if (!ApiUser::where('id', '=', $id)->exists()) {
//                return Feedback::getFeedback(501);
//            }
//        }
//
//        if (is_null($id)) {
//            $user = new ApiUser;
//            $user->password = hash('sha256', Settings::take('DEFAULT_PASSWORD'));
//            $user->access_token = uniqid();
//        } else {
//            $user = ApiUser::find($id);
//        }
//
//        if ($email == "") {
//            return Feedback::getFeedback(502);
//        }
//
//        if ($surname == "") {
//            return Feedback::getFeedback(503);
//        }
//
//        if ($name == "") {
//            return Feedback::getFeedback(504);
//        }
//
//        if ($role == "") {
//            return Feedback::getFeedback(505);
//        }
//
//        if ($active !== "0" && $active !== "1") {
//            return Feedback::getFeedback(506);
//        }
//
//        if ($permission_expression == "") {
//            return Feedback::getFeedback(507);
//        }
//
//        $user->email = $email;
//        $user->surname = $surname;
//        $user->name = $name;
//        $user->role = $role;
//        $user->active = $active;
//        $user->permission_expression = $permission_expression;
//        $user->save();
//
//        return Feedback::getFeedback(0);
//    }
//
//    public function setDefaultPassword()
//    {
//        $id = Input::get('id', null);
//
//        if (!ApiUser::where('id', '=', $id)->exists()) {
//            return Feedback::getFeedback(501);
//        }
//
//        $user = ApiUser::find($id);
//        $user->password = hash('sha256', Settings::take('DEFAULT_PASSWORD'));
//        $user->save();
//
//        return Feedback::getFeedback(0);
//    }
//
//    public function delete(Request $request)
//    {
//
//        $id = Input::get('id', null);
//        if (is_null($id)) {
//            return Feedback::getFeedback(501);
//        }
//
//        $user = ApiUser::find($id);
//        if (!$user->exists()) {
//            return Feedback::getFeedback(501);
//        }
//
//        try {
//            $user->delete();
//        } catch (QueryException $e) {
//            return Feedback::getFeedback(206);
//        }
//
//
//        return Feedback::getFeedback(0);
//    }
//
//    public function get()
//    {
//
//        $email = Input::get('email', '');
//        $surname = Input::get('surname', '');
//        $name = Input::get('name', '');
//        $role = Input::get('role', '');
//        $active = Input::get('active', '');
//
//        $items = DB::table('api_users')
//            ->where('email', 'like', '%' . $email . '%')
//            ->where('surname', 'like', '%' . $surname . '%')
//            ->where('name', 'like', '%' . $name . '%')
//            ->where('role', 'like', '%' . $role . '%')
//            ->where('active', 'like', '%' . $active . '%')
//            ->select(['id', 'name', 'surname', 'email', 'role', 'active', 'permission_expression'])
//            ->orderBy('surname', 'asc')
//            ->orderBy('name', 'asc')
//            ->get();
//
//
//        return Feedback::getFeedback(0, [
//            'items' => $items->toArray(),
//        ]);
//
//    }
//
//
//    public function changePassword(Request $request)
//    {
//
//        if (!Input::has('new_password')) {
//            return Feedback::getFeedback(105);
//        }
//
//        $token = $request->input('access_token');
//        $user = ApiUser::where('access_token', $token)->first();
//        $user->password = $request->input('new_password');
//        $user->save();
//
//        return Feedback::getFeedback(0);
//
//    }
}
