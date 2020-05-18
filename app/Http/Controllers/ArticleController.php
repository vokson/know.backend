<?php

namespace App\Http\Controllers;

use App\Article;
use App\Exceptions\Article\Delete\VersionIsNotLatest;
use App\Exceptions\Article\Set\MissedArticleWithId;
use Illuminate\Http\Request;
use App\Exceptions\Article\Validation\Uin;
use App\Exceptions\Article\Validation\Subject;
use App\Exceptions\Article\Validation\Body;
use App\Exceptions\Article\Validation\Version;
use App\Exceptions\Article\Validation\Owner;
use App\Exceptions\Article\Get\NullArticle As GetNullArticle;
use App\Exceptions\Article\Delete\NullArticle as DeleteNullArticle;
use App\Http\Controllers\FeedbackController as Feedback;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public static function validateUin($value)
    {
        throw_if(
            !(is_null($value) || ((ctype_digit($value) || is_int($value)) && $value > 0)),
            new Uin()
        );

        return true;
    }

    public static function validateVersion($value)
    {
        throw_if(
            !(is_null($value) || ((ctype_digit($value) || is_int($value)) && $value > 0)),
            new Version()
        );

        return true;
    }

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
        self::validateUin($request->input('uin'));
        self::validateSubject($request->input('subject'));
        self::validateBody($request->input('body'));

        $uin = $request->input('uin');
        $uin = (is_null($uin)) ? $uin : intval($uin);

        throw_if(!is_null($uin) && is_null(Article::where('uin', $uin)->first()), new MissedArticleWithId());

        $version = 1;

        if (is_null($uin)) {
            $max = Article::max('uin');
            $uin = (is_null($max)) ? 1 : $max + 1;
        } else {
            $version = intval(Article::where('uin', $uin)->max('version')) + 1;
        }

//        return Feedback::success([
//            'uin' => $uin,
//            'version' => $version
//        ]);

        $article = new Article();
        $article->uin = $uin;
        $article->version = $version;
        $article->subject = $request->input('subject');
        $article->body = $request->input('body');
        $article->user_id = AuthController::id($request);
        $article->save();

        return Feedback::success([
            'uin' => $article->uin,
            'version' => $article->version
        ]);
    }

    public function get(Request $request)
    {
        self::validateUin($request->input('uin'));
        self::validateVersion($request->input('version'));

        $uin = $request->input('uin');
        $uin = (is_null($uin)) ? $uin : intval($uin);

        $version = $request->input('version');
        $version = (is_null($version)) ? $version : intval($version);

        throw_if(is_null($uin), new Uin());

        $maxVersion =  Article::where('uin', $uin)->max('version');

        $article = null;
        if (is_null($version)) {
            $article = Article::where('uin', $uin)->where('version', Article::where('uin', $uin)->max('version'))->first();
        } else {
            $article = Article::where('uin', $uin)->where('version', $version)->first();
        }


        throw_if(is_null($article), new GetNullArticle());

        return Feedback::success([
            'uin' => $article->uin,
            'version' => $article->version,
            'max_version' => $maxVersion,
            'subject' => $article->subject,
            'body' => $article->body,
            'is_attachment_exist' => intval($article->is_attachment_exist),
            'owner' => AuthController::getSurnameAndNameOfUserById($article->user_id),
            'date' => $article->updated_at->getTimestamp(),
        ]);
    }

    public function delete(Request $request)
    {
        self::validateUin($request->input('uin'));
        self::validateVersion($request->input('version'));


        $uin = intval($request->input('uin'));
        $version = intval($request->input('version'));

        throw_if(is_null($uin), new Uin());
        throw_if(is_null($version), new Version());

        $article = Article::where('uin', $uin)->where('version', $version)->first();

        throw_if(is_null($article), new DeleteNullArticle());
        throw_if($version != Article::where('uin', $uin)->max('version'), new VersionIsNotLatest());

        Article::where('uin', $uin)->where('version', $version)->delete();

        return Feedback::success();
    }

    public function search(Request $request)
    {

        $uin = trim($request->input('uin', ''));
        $owner = trim($request->input('owner', ''));
        $subject = trim($request->input('subject', ''));
        $body = trim($request->input('body', ''));

        [$idUsers, $idNamesUsers] = $this->getNamesUsers($owner);

//        DB::enableQueryLog();

        $items = DB::table('articles')
            ->where('uin', 'like', '%' . $uin . '%')
            ->where('body', 'like', '%' . $body . '%')
            ->where('subject', 'like', '%' . $subject . '%')
            ->whereIn('owner', $idUsers)
            ->select(DB::raw('
                "uin",
                "is_attachment_exist",
                "user_id" as "owner", 
                "created_at" as "date",
                "subject", 
                max("version") as "version"
             '))
            ->groupBy('uin')
            ->orderBy('date', 'desc')
            ->get();

//        dd(DB::getQueryLog());

        // Подменяем uin на значения полей из других таблиц
        $items->transform(function ($item, $key) use ($idNamesUsers) {
            $item->owner = $idNamesUsers[$item->owner];
            return $item;
        });

        return Feedback::success([
            'items' => $items->toArray(),
        ]);
    }

    private function getNamesUsers($owner)
    {
//        DB::enableQueryLog();

        $users = DB::table('users')
            ->where('name', 'like', '%' . $owner . '%')
            ->orWhere('surname', 'like', '%' . $owner . '%')
            ->select('id', 'name', 'surname')
            ->get();

//        dd(DB::getQueryLog());

        $idUsers = $users->map(function ($item) {
            return $item->id;
        });

        $namesUsers = $users->map(function ($item) {
            return $item->surname . ' ' . $item->name;
        });

        return [$idUsers, array_combine($idUsers->toArray(), $namesUsers->toArray())];
    }


}
