<?php

namespace App\Http\Controllers;

Use App\User;
use App\Exceptions\User\Validation\Id;
use App\Exceptions\User\Validation\Active;
use App\Exceptions\User\Validation\Name;
use App\Exceptions\User\Validation\Surname;
use App\Exceptions\User\Validation\Role;
use App\Exceptions\User\Validation\Password;
use App\Exceptions\User\Validation\Email;
use App\Exceptions\User\Validation\PermissionExpression;
use App\Exceptions\User\Create\NotUniqueEmail;
Use App\Exceptions\User\Set\MissedUserWithId;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FeedbackController As Feedback;
use App\Http\Controllers\SettingController as Setting;

class UserController extends Controller
{

    public static function validateId($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value <= 0,
            new Id()
        );

        return true;
    }

    public static function validateActive($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
            new Active()
        );

        return true;
    }

    public static function validateName($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || strlen(trim($value)) == 0,
            new Name()
        );

        return true;
    }

    public static function validateSurname($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || strlen(trim($value)) == 0,
            new Surname()
        );

        return true;
    }

    public static function validateRole($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '' || strlen(trim($value)) == 0,
            new Role()
        );

        return true;
    }

    public static function validateEmail($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '' || !filter_var($value, FILTER_VALIDATE_EMAIL),
            new Email()
        );

        return true;
    }

    public static function validatePermissionExpression($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || $value === '',
            new PermissionExpression()
        );

        return true;
    }

    public static function validatePassword($value)
    {
        throw_if(
            is_null($value) || !is_string($value) || strlen($value) < 3,
            new Password()
        );

        return true;
    }

    public function create(Request $request)
    {
        self::validateActive($request->input('active'));
        self::validateName($request->input('name'));
        self::validateSurname($request->input('surname'));
        self::validateRole($request->input('role'));
        self::validateEmail($request->input('email'));
        self::validatePermissionExpression($request->input('permission_expression'));

        $user = new User();
        $user->active = $request->input('active');
        $user->name = trim($request->input('name'));
        $user->surname = trim($request->input('surname'));
        $user->role = trim($request->input('role'));
        $user->email = trim($request->input('email'));

        $userWithEmail = User::where('email', $user->email)->first();
        throw_if(!is_null($userWithEmail), new NotUniqueEmail());

        $user->permission_expression = trim($request->input('permission_expression'));
        $user->password = hash('sha256', Setting::take('DEFAULT_PASSWORD'));
        $user->access_token = bin2hex(random_bytes(30));
        $user->save();

        return Feedback::success();
    }

    public function set(Request $request)
    {

        self::validateId($request->input('id'));
        self::validateActive($request->input('active'));
        self::validateName($request->input('name'));
        self::validateSurname($request->input('surname'));
        self::validateRole($request->input('role'));
        self::validateEmail($request->input('email'));
        self::validatePermissionExpression($request->input('permission_expression'));

        $user = User::where('id', $request->input('id'))->first();
        throw_if(is_null($user), new MissedUserWithId());

        $user->active = $request->input('active');
        $user->name = trim($request->input('name'));
        $user->surname = trim($request->input('surname'));
        $user->role = trim($request->input('role'));
        $user->email = trim($request->input('email'));

        $userWithEmail = User::where('email', $user->email)->first();

        if (!is_null($userWithEmail)) {
            if ($userWithEmail->id !== $user->id) {
                throw new NotUniqueEmail();
            }
        }

        $user->permission_expression = trim($request->input('permission_expression'));
        $user->save();

        return Feedback::success();
    }

    public function delete(Request $request)
    {

        self::validateId($request->input('id'));

        $user = User::where('id', $request->input('id'))->first();
        throw_if(is_null($user), new MissedUserWithId());

        $user->delete();

        return Feedback::success();
    }


    public function setDefaultPasswordToUserWithId(Request $request)
    {
        self::validateId($request->input('id'));

        $user = User::where('id', $request->input('id'))->first();
        throw_if(is_null($user), new MissedUserWithId());

        $user->password = hash('sha256', Settings::take('DEFAULT_PASSWORD'));
        $user->save();

        return Feedback::success();
    }


    public function get(Request $request)
    {
        $active = $request->input('active', '');

        $email = $request->input('email', '');
        $surname = $request->input('surname', '');
        $name = $request->input('name', '');
        $role = $request->input('role', '');



        $items = DB::table('users')
            ->where('email', 'like', '%' . $email . '%')
            ->where('surname', 'like', '%' . $surname . '%')
            ->where('name', 'like', '%' . $name . '%')
            ->where('role', 'like', '%' . $role . '%')
            ->where('active', 'like', '%' . $active . '%')
            ->select(['id', 'name', 'surname', 'email', 'role', 'active', 'permission_expression'])
            ->orderBy('surname', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Feedback::success([
            'items' => $items->toArray(),
        ]);

    }

    public function changePassword(Request $request)
    {
        self::validatePassword($request->input('password'), '');

        $user = User::find(AuthController::currentUsedId());
        $user->password = $request->input('password');
        $user->save();

        return Feedback::success();
    }

}
