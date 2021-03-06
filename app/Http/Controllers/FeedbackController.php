<?php

namespace App\Http\Controllers;


class FeedbackController extends Controller
{
    public static function success($feedback = [])
    {
        $feedback['success'] = 1;
        return json_encode($feedback);
    }

    public static function error(\Exception $e)
    {
        $feedback = [];
        $feedback['success'] = 0;
        $feedback['error'] = self::getCodeByException($e);
        $feedback['description'] = $e->getMessage();
        return json_encode($feedback);
    }

    private static function getCodeByException(\Exception $e)
    {
        //Auth
        if ($e instanceof \App\Exceptions\User\Login\InvalidLoginPassword) {
            return '1.1';
        } elseif ($e instanceof \App\Exceptions\User\Login\UserSwitchedOff) {
            return '1.2';
        } elseif ($e instanceof \App\Exceptions\User\Login\InvalidToken) {
            return '1.3';
        } elseif ($e instanceof \App\Exceptions\User\Login\DeadToken) {
            return '1.4';
        } elseif ($e instanceof \App\Exceptions\User\Permission\RouteAccessDenied) {
            return '1.5';

            // User
        } elseif ($e instanceof \App\Exceptions\User\Validation\Active) {
            return '2.1';
        } elseif ($e instanceof \App\Exceptions\User\Validation\Email) {
            return '2.2';
        } elseif ($e instanceof \App\Exceptions\User\Validation\Name) {
            return '2.3';
        } elseif ($e instanceof \App\Exceptions\User\Validation\Surname) {
            return '2.4';
        } elseif ($e instanceof \App\Exceptions\User\Validation\Role) {
            return '2.5';
        } elseif ($e instanceof \App\Exceptions\User\Validation\PermissionExpression) {
            return '2.6';
        } elseif ($e instanceof \App\Exceptions\User\Create\NotUniqueEmail) {
            return '2.7';
        } elseif ($e instanceof \App\Exceptions\User\Set\MissedUserWithId) {
            return '2.8';


            // Setting
        } elseif ($e instanceof \App\Exceptions\Setting\Validation\Items) {
            return '3.1';
        } elseif ($e instanceof \App\Exceptions\Setting\Validation\Name) {
            return '3.2';
        } elseif ($e instanceof \App\Exceptions\Setting\Validation\Value) {
            return '3.3';
        } elseif ($e instanceof \App\Exceptions\Setting\Set\MissedName) {
            return '3.4';

            // Action
        } elseif ($e instanceof \App\Exceptions\Action\Validation\Role) {
            return '4.1';
        } elseif ($e instanceof \App\Exceptions\Action\Validation\Name) {
            return '4.2';
        } elseif ($e instanceof \App\Exceptions\Action\Validation\State) {
            return '4.3';
        } elseif ($e instanceof \App\Exceptions\Action\Validation\Items) {
            return '4.4';

            // Article
        } elseif ($e instanceof \App\Exceptions\Article\Validation\Subject) {
            return '5.1';
        } elseif ($e instanceof \App\Exceptions\Article\Validation\Body) {
            return '5.2';
        } elseif ($e instanceof \App\Exceptions\Article\Validation\Uin) {
            return '5.3';
        } elseif ($e instanceof \App\Exceptions\Article\Validation\IsAttachmentExist) {
            return '5.4';
        } elseif ($e instanceof \App\Exceptions\Article\Set\MissedArticleWithId) {
            return '5.5';
        } elseif ($e instanceof \App\Exceptions\Article\Get\NullArticle) {
            return '5.6';
        } elseif ($e instanceof \App\Exceptions\Article\Delete\NullArticle) {
            return '5.7';
        } elseif ($e instanceof \App\Exceptions\Article\Delete\VersionIsNotLatest) {
            return '5.8';
        } elseif ($e instanceof \App\Exceptions\Article\Validation\Version) {
            return '5.9';
        } elseif ($e instanceof \App\Exceptions\Article\Validation\Query) {
            return '5.10';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Delete\AttachedFilesExists) {
            return '5.11';

            // Tag
        } elseif ($e instanceof \App\Exceptions\Tag\Validation\Id) {
            return '6.1';
        } elseif ($e instanceof \App\Exceptions\Tag\Validation\Name) {
            return '6.2';
        } elseif ($e instanceof \App\Exceptions\Tag\Add\MissedArticleWithId) {
            return '6.3';
        } elseif ($e instanceof \App\Exceptions\Tag\Add\NameHasNotCreated) {
            return '6.4';
        } elseif ($e instanceof \App\Exceptions\Tag\Validation\Items) {
            return '6.5';

            // Article File
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Validation\Id) {
            return '7.1';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Validation\Uin) {
            return '7.2';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Upload\FileCanNotBeStored) {
            return '7.3';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Upload\InvalidFile) {
            return '7.4';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Upload\MissedFile) {
            return '7.5';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Upload\NullArticle) {
            return '7.6';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Download\MissedFileInDatabase) {
            return '7.7';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Download\MissedFileInStorage) {
            return '7.8';
        } elseif ($e instanceof \App\Exceptions\ArticleFile\Delete\DeletionError) {
            return '7.9';


            // Zip
        } elseif ($e instanceof \App\Exceptions\Zip\Create\CanNotBeCreated) {
            return '8.1';

            // DEFAULT
        } else {
            return '0.0';
        }

    }

}
