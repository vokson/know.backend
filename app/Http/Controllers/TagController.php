<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FeedbackController as Feedback;
use App\Tag;
use Illuminate\Http\Request;
use App\Exceptions\Tag\Validation\Id;
use App\Exceptions\Tag\Validation\Name;

class TagController extends Controller
{
    public static function validateId($value)
    {
        throw_if(
            !( (is_int($value) && $value > 0)),
            new Id()
        );

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

    public function list(Request $request) {
        $tags = Tag::whereNull('article_id')->get()->toArray();
        $tags = array_column($tags, 'name');

        return Feedback::success([
            'items' => $tags,
        ]);
    }

    public function create(Request $request)
    {
        self::validateName($request->input('name'));
        $name = trim($request->input('name'));

        if (Tag::where('name', $name)->count()  == 0) {
            $tag = new Tag();
            $tag->name = $name;
            $tag->save();
        }

        return Feedback::success();
    }

    public function delete(Request $request)
    {
        self::validateName($request->input('name'));

        $name = trim($request->input('name'));
        Tag::where('name', $name)->delete();

        return Feedback::success();
    }

}
