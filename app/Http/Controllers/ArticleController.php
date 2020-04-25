<?php

namespace App\Http\Controllers;

use App\Article;
use App\Exceptions\Article\Delete\VersionIsNotLatest;
use App\Exceptions\Article\Set\MissedArticleWithId;
use App\Exceptions\User\Set\MissedUserWithId;
use App\User;
use DemeterChain\A;
use Illuminate\Http\Request;
use App\Exceptions\Article\Validation\Id;
use App\Exceptions\Article\Validation\Subject;
use App\Exceptions\Article\Validation\Body;
use App\Exceptions\Article\Validation\Version;
use App\Exceptions\Article\Validation\Owner;
use App\Exceptions\Article\Get\NullArticle As GetNullArticle;
use App\Exceptions\Article\Delete\NullArticle as DeleteNullArticle;
use App\Http\Controllers\FeedbackController as Feedback;

class ArticleController extends Controller
{
    public static function validateId($value)
    {
        throw_if(
            !(is_null($value) || (is_int($value) && $value > 0)),
            new Id()
        );

        return true;
    }

    public static function validateVersion($value)
    {
        throw_if(
            !(is_null($value) || (is_int($value) && $value > 0)),
            new Version()
        );

        return true;
    }

//    public static function validateUserId($value)
//    {
//
//        throw_if(
//            is_null($value) || !is_int($value) || $value <= 0,
//            new UserId()
//        );
//
//        throw_if(is_null(User::find($value)), new UserId());
//
//        return true;
//    }

//    public static function validateIsAttachmentExist($value)
//    {
//        throw_if(
//            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
//            new IsAttachmentExist()
//        );
//
//        return true;
//    }

    public static function validateString($value)
    {
        return (!is_null($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    public static function validateSubject($value)
    {
        throw_if(!self::validateString($value), new Subject());

        return true;
    }

    public static function validateBody($value)
    {
        throw_if(!self::validateString($value), new Body());

        return true;
    }

    public static function validateOwner($value)
    {
        throw_if(!self::validateString($value), new Owner());

        return true;
    }

    public function set(Request $request)
    {
        self::validateId($request->input('id'));
        self::validateSubject($request->input('subject'));
        self::validateBody($request->input('body'));

        $id = $request->input('id');
        throw_if(!is_null($id) && is_null(Article::where('id', $id)->first()), new MissedArticleWithId());

        $version = 1;

        if (is_null($id)) {
            $max = Article::max('id');
            $id = (is_null($max)) ? 1 : $max + 1;
        } else {
            $version = intval(Article::where('id', $id)->max('version')) + 1;
        }

        $article = new Article();
        $article->id = $id;
        $article->version = $version;
        $article->subject = $request->input('subject');
        $article->body = $request->input('body');
        $article->user_id = AuthController::id($request);
        $article->save();

        return Feedback::success();
    }

    public function get(Request $request)
    {
        self::validateId($request->input('id'));
        self::validateVersion($request->input('version'));

        $id = $request->input('id');
        $version = $request->input('version');

        throw_if(is_null($id), new Id());


        $article = null;
        if (is_null($version)) {
            $article = Article::where('id', $id)->where('version', Article::where('id', $id)->max('version'))->first();
        } else {
            $article = Article::where('id', $id)->where('version', $version)->first();
        }


        throw_if(is_null($article), new GetNullArticle());

        return Feedback::success([
            'id' => $article->id,
            'version' => $article->version,
            'subject' => $article->subject,
            'body' => $article->body,
            'is_attachment_exist' => intval($article->is_attachment_exist),
            'owner' => AuthController::getSurnameAndNameOfUserById($article->user_id)
        ]);
    }

    public function delete(Request $request)
    {
        self::validateId($request->input('id'));
        self::validateVersion($request->input('version'));

        $id = $request->input('id');
        $version = $request->input('version');

        throw_if(is_null($id), new Id());
        throw_if(is_null($version), new Version());

        $article = Article::where('id', $id)->where('version', $version)->first();

        throw_if(is_null($article), new DeleteNullArticle());
        throw_if($version != Article::where('id', $id)->max('version'), new VersionIsNotLatest());

        Article::where('id', $id)->where('version', $version)->delete();

        return Feedback::success();
    }

    public function getListOfArticles(Request $request)
    {
        self::validateId($request->input('id'));
        self::validateSubject($request->input('subject'));
        self::validateBody($request->input('body'));
        self::validateOwner($request->input('owner'));

        $id = $request->input('id');
        $version = $request->input('version');

        throw_if(is_null($id), new Id());


        $article = null;
        if (is_null($version)) {
            $article = Article::where('id', $id)->where('version', Article::where('id', $id)->max('version'))->first();
        } else {
            $article = Article::where('id', $id)->where('version', $version)->first();
        }


        throw_if(is_null($article), new GetNullArticle());

        return Feedback::success([
            'id' => $article->id,
            'version' => $article->version,
            'subject' => $article->subject,
            'body' => $article->body,
            'is_attachment_exist' => intval($article->is_attachment_exist),
            'owner' => AuthController::getSurnameAndNameOfUserById($article->user_id)
        ]);
    }

}
