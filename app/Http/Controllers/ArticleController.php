<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use App\Exceptions\Article\Delete\VersionIsNotLatest;
use App\Exceptions\Article\Set\MissedArticleWithId;
use App\Exceptions\ArticleFile\Delete\AttachedFilesExists;
use Illuminate\Http\Request;
use App\Exceptions\Article\Validation\Uin;
use App\Exceptions\Article\Validation\Subject;
use App\Exceptions\Article\Validation\Body;
use App\Exceptions\Article\Validation\Version;
use App\Exceptions\Article\Validation\Query;
use App\Exceptions\Article\Get\NullArticle As GetNullArticle;
use App\Exceptions\Article\Delete\NullArticle as DeleteNullArticle;
use App\Http\Controllers\FeedbackController as Feedback;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Tag;

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

    public static function validateQuery($value)
    {
        throw_if(!self::validateString($value), new Query());

        return true;
    }

    private function strToLowerCase($s)
    {
        // Преобразуем спец символы HTML обратно
        $r = html_entity_decode($s, ENT_COMPAT | ENT_HTML401, 'UTF-8');
        // Удаляем все тэги html
        $r = strip_tags($r);
        // Преобразование строки к нижнему регистру
        return mb_strtolower($r, 'UTF-8');
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
        $is_attachment_exist = false;

        if (is_null($uin)) {
            $max = Article::max('uin');
            $uin = (is_null($max)) ? 1 : $max + 1;
        } else {
            $version = intval(Article::where('uin', $uin)->max('version'));
            $article = Article::where('uin', $uin)->where('version', $version)->first();

            $is_attachment_exist = $article->is_attachment_exist;
            $version++;
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
        $article->lowered_subject = $this->strToLowerCase($request->input('subject'));
        $article->lowered_body = $this->strToLowerCase($request->input('body'));
        $article->user_id = AuthController::id($request);
        $article->is_attachment_exist = $is_attachment_exist;
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

        $maxVersion = Article::where('uin', $uin)->max('version');

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
        throw_if(
            ArticleFile::where('article_id', $article->uin)->count() > 0 && $version == 1,
            new AttachedFilesExists()
        );

        Article::where('uin', $uin)->where('version', $version)->delete();

        if ($version == 1) {
            Tag::where('article_id', $uin)->delete();
        }


        return Feedback::success([
            'uin' => $article->uin,
            'version' => $article->version
        ]);
    }

    public function search(Request $request)
    {
        $date1 = intval(trim($request->input('date1', '')));
        $date2 = intval(trim($request->input('date2', '')));

        self::validateQuery($request->input('query'));
        $query = trim($request->input('query', ''));
        $query = preg_replace('/\s+/', ' ', $query);

        // Разбиваем запрос на фильтры author:
        $queryArr = explode(' ', mb_strtolower($query, 'UTF-8'));
        $wordsToBeSearched = [];
        $uin = '';
        $owner = '';
        $subject = '';

        foreach ($queryArr as $word) {
            if (
                strlen($word) > strlen('author:') &&
                substr($word, 0, strlen('author:')) == 'author:'
            ) {
                $owner = substr($word, strlen('author:'));

            } elseif (
                strlen($word) > strlen('subject:') &&
                substr($word, 0, strlen('subject:')) == 'subject:'
            ) {
                $subject = substr($word, strlen('subject:'));

            } elseif (
                strlen($word) > strlen('uin:') &&
                substr($word, 0, strlen('uin:')) == 'uin:'
            ) {
                $uin = substr($word, strlen('uin:'));

            } else {
                $wordsToBeSearched[] = $word;
            }
        }

        [$idUsers, $idNamesUsers] = $this->getNamesUsers($owner);

        //DATE
        $dayStartDate = 1;
        $dayEndDate = 9999999999;

        if ($date1 != '' && $date2 != '') {
            $dayStartDate = DateTime::createFromFormat('U', min($date1, $date2))->setTime(0, 0, 0)->format('U');
            $dayEndDate = DateTime::createFromFormat('U', max($date1, $date2))->setTime(23, 59, 59)->format('U');
        }

//        DB::enableQueryLog();

        $items = DB::table('articles')
            ->whereBetween('updated_at', [$dayStartDate, $dayEndDate])
            ->where('uin', 'like', '%' . $uin . '%')
            ->where(function ($query) use ($wordsToBeSearched) {

                if (count($wordsToBeSearched) > 0) {
                    for ($i = 0; $i < count($wordsToBeSearched); $i++) {
                        $query->where('lowered_body', 'like', '%' . $wordsToBeSearched[$i] . '%');
                    }
                }

            })
            ->where('lowered_subject', 'like', '%' . $subject . '%')
            ->whereIn('user_id', $idUsers)
//            ->leftJoin('tags', 'articles.uin', '=', 'tags.article_id')
            ->select(DB::raw('
                "uin",
                "is_attachment_exist",
                "user_id" as "owner", 
                "created_at" as "date",
                "subject", 
                "body",
                "lowered_body",
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

    public static function setAttachmentStatus($article_id, $status) {
        Article::where('uin', $article_id)->update(['is_attachment_exist' => $status]);
    }


}
