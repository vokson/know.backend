<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Exceptions\Article\Validation\Id;
use App\Exceptions\Article\Validation\Subject;
use App\Exceptions\Article\Validation\Body;
use App\Exceptions\Article\Validation\IsAttachmentExist;
use App\Exceptions\Article\Validation\UserId;

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

    public static function validateUserId($value)
    {

        throw_if(
            is_null($value) || !is_int($value) || $value <= 0,
            new UserId()
        );

        throw_if(is_null(User::find($value)), new UserId());

        return true;
    }

    public static function validateIsAttachmentExist($value)
    {
        throw_if(
            is_null($value) || !is_int($value) || $value < 0 || $value > 1,
            new IsAttachmentExist()
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

}
