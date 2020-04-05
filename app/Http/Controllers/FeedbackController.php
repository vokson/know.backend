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
        $feedback['notifications'] = self::getNotificationsByException($e);
        return json_encode($feedback);
    }

    private static function getCodeByException(\Exception $e)
    {
        // User
        if ($e instanceof \App\Exceptions\User\Validation\Active) {
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
        } else {
            return '0.0';
        }

    }

    /**
     * @param \Exception $e
     * @return array
     */
    private static function getNotificationsByException(\Exception $e)
    {
        if ($e instanceof UserDecisionRequired) return Model::getErrors();
        return [];
    }
}
