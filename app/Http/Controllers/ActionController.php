<?php

namespace App\Http\Controllers;

use App\Action;
use App\Exceptions\Action\Validation\State;
use App\Exceptions\Action\Validation\Role;
use App\Exceptions\Action\Validation\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController as Feedback;

class ActionController extends Controller
{
    public function validateState($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
            new State()
        );

        return true;
    }

    public function validateString($value)
    {
        return (!is_null($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    public function validateName($value)
    {
        throw_if(!$this->validateString($value), new Name());

        return true;
    }

    public function validateRole($value)
    {
        throw_if(!$this->validateString($value), new Role());

        return true;
    }

    public function get(Request $request)
    {
        $items = DB::table('actions')
            ->whereNotNull('role')
            ->select(['name', 'role'])
            ->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Feedback::success([
            'items' => $items->toArray(),
        ]);

    }

    public function set(Request $request)
    {
        $state = $request->input('state', null);
        $role = $request->input('role', null);
        $name = $request->input('name', null);

        $this->validateState($state);
        $this->validateRole($role);
        $this->validateName($name);


        Action::where('name', $name)->where('role', $role)->delete();

        if ($state === 1) {
            $a = new Action(['role' => $role, 'name' => $name]);
            $a->save();
        }

        return Feedback::success();

    }
}
