<?php

namespace App\Http\Controllers;

use App\Article;
use App\Exceptions\Article\Set\MissedArticleWithId;
use App\Exceptions\User\Set\MissedUserWithId;
use App\User;
use DemeterChain\A;
use Illuminate\Http\Request;
use App\Exceptions\Article\Validation\Id;
use App\Exceptions\Article\Validation\Subject;
use App\Exceptions\Article\Validation\Body;
use App\Exceptions\Article\Validation\IsAttachmentExist;
use App\Exceptions\Article\Validation\UserId;
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

    public function set(Request $request)
    {
        self::validateId($request->input('id'));
        self::validateSubject($request->input('subject'));
        self::validateBody($request->input('body'));



        $id = $request->input('id');
        throw_if(!is_null($id) && is_null(Article::where('id', $id)->first()), new MissedArticleWithId());



        $version = 1;
        if (is_null($id)) {
            $max =  Article::max('id');
            $id = (is_null($max)) ? 1 : $max + 1;
        } else {
            $version = Article::where('id',$id)->max('version');
        }

        $article = new Article();
        $article -> id = $id;
        $article->version = $version;
        $article->subject = $request->input('subject');
        $article->body = $request->input('body');
        $article->user_id = AuthController::id($request);
        $article->save();

        return Feedback::success();
    }

}
