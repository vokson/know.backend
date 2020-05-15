<?php

namespace App\Http\Controllers;

use App\Action;
use App\Exceptions\Action\Validation\State;
use App\Exceptions\Action\Validation\Role;
use App\Exceptions\Action\Validation\Name;
use App\Exceptions\Action\Validation\Items;
use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController as Feedback;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    public static function take($role, $name)
    {
        self::validateRole($role);
        self::validateName($name);

        $action = Action::where('name', $name)->where('role', $role)->first();
        return !is_null($action);
    }

    public static function validateRole($value)
    {
        throw_if(!self::validateString($value), new Role());

        return true;
    }

    public static function validateString($value)
    {
        return (!is_null($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    public static function validateName($value)
    {
        throw_if(!self::validateString($value), new Name());

        return true;
    }


    public static function validateState($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
            new State()
        );

        return true;
    }

    public static function validateItems($value)
    {
        throw_if(is_null($value) || !is_array($value), new Items());

        return true;
    }

    public function get(Request $request)
    {


        $actionList = DB::table('actions')
            ->whereNull('role')
            ->groupBy('name')
            ->select(['name'])
            ->orderBy('name')
            ->get();

        $func = function ($item) {
            return $item->name;
        };

        $actionList = array_map($func, $actionList->toArray());
        $roleList = UserController::getListOfRoles();

        $pairList = [];
        foreach ($actionList as $action) {
            foreach ($roleList as $role) {
                $state = Action::where('name', $action)->where('role', $role)->first();
                $pairList[$action][$role] = (is_null($state)) ? 0 : 1;
            }
        }

        return Feedback::success([
            'items' => $pairList,
        ]);

    }

//    public function get(Request $request)
//    {
//        $items = DB::table('actions')
//            ->whereNotNull('role')
//            ->select(['name', 'role'])
//            ->get();
//
//        return Feedback::success([
//            'items' => $items->toArray(),
//        ]);
//
//    }

    public function getListOfRoles(Request $request)
    {
        $items = DB::table('actions')
            ->whereNotNull('role')
            ->groupBy('role')
            ->select(['role'])
            ->orderBy('role')
            ->get();


        $func = function ($item) {
            return $item->role;
        };


        return Feedback::success([
            'items' => array_map($func, $items->toArray()),
        ]);

    }

    public function getListOfActions(Request $request)
    {
        $items = DB::table('actions')
            ->whereNotNull('name')
            ->groupBy('name')
            ->select(['name'])
            ->orderBy('name')
            ->get();

        $func = function ($item) {
            return $item->name;
        };

        return Feedback::success([
            'items' => array_map($func, $items->toArray()),
        ]);

    }

    public function set(Request $request)
    {
//        $state = $request->input('state', null);
//        $role = $request->input('role', null);
//        $name = $request->input('name', null);
//
//        self::validateState($state);
//        self::validateRole($role);
//        self::validateName($name);


        $items = $request->input('items', null);
        self::validateItems($items);


        try {
            foreach ($items as $name => $roles) {
                self::validateName($name);

//                return Feedback::success([
////                    'name' => $name,
////                    'roles' => $roles
////                ]);

                foreach ($roles as $role => $state) {

                    self::validateState($state);
                    self::validateRole($role);

                    Action::where('name', $name)->where('role', $role)->delete();

                    if ($state === 1) {
                        $a = new Action(['role' => $role, 'name' => $name]);
                        $a->save();
                    }

                }
            }

        } catch (Name $e) {
            throw new Name();
        } catch (Role $e) {
            throw new Role();
        } catch (State $e) {
            throw new State();
        } catch (\Exception $e) {
            throw new Items();
        }

        return Feedback::success();

    }

}
