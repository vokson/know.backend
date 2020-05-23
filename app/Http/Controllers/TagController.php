<?php

namespace App\Http\Controllers;

use App\Article;
use App\Exceptions\Tag\Validation\Items;
use App\Exceptions\Tag\Add\MissedArticleWithId;
use App\Exceptions\Tag\Add\NameHasNotCreated;
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
            !(is_int($value) && $value > 0),
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

    public static function validateItems($value)
    {
        throw_if(is_null($value) || !is_array($value), new Items());

        return true;
    }

    public function list(Request $request)
    {
        $tags = Tag::whereNull('article_id')->orderBy('name', 'asc')->get()->toArray();
        $tags = array_column($tags, 'name');

        return Feedback::success([
            'items' => $tags,
        ]);
    }

    public function create(Request $request)
    {
        self::validateName($request->input('name'));
        $name = trim($request->input('name'));

        if (Tag::where('name', $name)->count() == 0) {
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

//    public function add(Request $request)
//    {
//        self::validateName($request->input('name'));
//        self::validateId($request->input('id'));
//
//
//        $id = (int)($request->input('id'));
//        $name = trim($request->input('name'));
//
//
//
//        throw_if(Tag::where('name', $name)->whereNull('article_id')->count() == 0, new NameHasNotCreated());
//        throw_if(Article::where('id', $id)->count() == 0, new MissedArticleWithId());
//
//        $tag = new Tag();
//        $tag->name = $name;
//        $tag->article_id = $id;
//        $tag->save();
//
//        return Feedback::success();
//    }
//
//    public function remove(Request $request)
//    {
//        self::validateId($request->input('id'));
//        self::validateName($request->input('name'));
//
//        $id = (int) ($request->input('id'));
//        $name = trim($request->input('name'));
//
//        Tag::where('name', $name)->where('article_id', $id)->delete();
//
//        return Feedback::success();
//    }

    public function get(Request $request)
    {
        self::validateId($request->input('id'));
        $id = (int)($request->input('id'));

        $tags = Tag::where('article_id', $id)->orderBy('name', 'asc')->get()->toArray();
        $tags = array_column($tags, 'name');

        return Feedback::success([
            'id' => $id,
            'items' => $tags,
        ]);

    }

    public function getForMany(Request $request)
    {

        $ids = $request->input('id_list', null);
        self::validateItems($ids);

//        function makeInt(&$item)
//        {
//            $item = (int)$item;
//        }
//
//        array_walk($ids, 'makeInt');

        foreach ($ids as &$id) {
            $id = (int)$id;
            self::validateId($id);
        }

        $tags = Tag::whereIn('article_id', $ids)->get()->toArray();

        $tagByIdArray = [];
        $tagList = [];

        foreach ($tags as $tag) {
            $id = $tag['article_id'];
            $name = $tag['name'];
            $tagList[$name] = true;

            if (key_exists($id, $tagByIdArray)) {
                $tagByIdArray[$id][] = $name;

            } else {
                $tagByIdArray[$id] = [$name];
            }

        }

        $tagList = array_keys($tagList);
        sort($tagList);

        return Feedback::success([
            'list_by_id' => $tagByIdArray,
            'list_of_names' => $tagList
        ]);

    }

    public function set(Request $request)
    {
        self::validateId($request->input('id'));
        $id = (int)($request->input('id'));

        throw_if(Article::where('id', $id)->count() == 0, new MissedArticleWithId());

        $names = $request->input('items', null);
        self::validateItems($names);

        foreach ($names as $name) {
            self::validateName($name);
            throw_if(Tag::where('name', $name)->whereNull('article_id')->count() == 0, new NameHasNotCreated());
        }

        Tag::where('article_id', $id)->delete();

        foreach ($names as $name) {
            $tag = new Tag();
            $tag->name = $name;
            $tag->article_id = $id;
            $tag->save();
        }

        return Feedback::success([
            'id' => $id
        ]);
    }

}
