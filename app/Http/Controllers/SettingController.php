<?php

namespace App\Http\Controllers;

use App\Exceptions\Setting\Set\SaveError;
use Illuminate\Http\Request;
use App\Setting;
use App\Http\Controllers\FeedbackController As Feedback;
Use App\Exceptions\Setting\Validation\Name;
Use App\Exceptions\Setting\Validation\Value;
use App\Exceptions\Setting\Validation\Items;

class SettingController extends Controller
{

    public function validateString($value)
    {
        return (!is_null($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    public function validateName($value)
    {
        throw_if(!$this->validateString($value), new Name());

        return true;
    }

    public function validateValue($value)
    {
        throw_if(!$this->validateString($value), new Value());

        return true;
    }

    public function validateItems($value)
    {
        throw_if(is_null($value) || !is_array($value), new Items());

        return true;
    }

    public static function take(string $name)
    {
        $parameter = Setting::where('name', $name)->first();
        if ($parameter) return $parameter->value;
    }

    public static function save(string $name, string $value)
    {
        $parameter = Setting::where('name', $name)->first();
        if (is_null($parameter)) return false;

        $parameter->value = $value;
        $parameter->save();
        return true;
    }

    public function get(Request $request)
    {
        $parameters = [];

        foreach (Setting::all() as $item) {
            $parameters[] = array_filter($item->toArray(), function ($k) {
                return ($k == 'name' || $k == 'value');
            }, ARRAY_FILTER_USE_KEY);
        }

        return Feedback::success([
            "items" => $parameters
        ]);

    }


    public function set(Request $request)
    {
        $items = $request->input('items', null);
        $this->validateItems($items);

        foreach ($items as $item) {

            $name = array_key_exists('name', $item) ? $item['name'] : null;
            $value = array_key_exists('value', $item) ? $item['value'] : null;

            $this->validateName($name);
            $this->validateValue($value);

            throw_if(!self::save($name, $value), new SaveError());

        }

        return Feedback::success();

    }


}
