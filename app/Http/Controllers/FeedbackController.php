<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Exception\FileCanNotBeStored;
use App\Http\Controllers\Exception\WrongRequestInput;
//use App\Plasticine\Collection\Exception\AddAlreadyExistHashException;
//use App\Plasticine\Collection\Exception\AddWrongTypeException;
//use App\Plasticine\Import\Json\Exception\InvalidJsonFormat;
//use App\Plasticine\Import\Json\Exception\MissedPrimaryProperty;
//use App\Plasticine\Item\Exception\KeyIsNotAllowedException;
//use App\Plasticine\Item\Exception\PropertyWithKeyMissedException;
//use App\Plasticine\Merge\Exception\InvalidArgumentClass;
//use App\Plasticine\Merge\Exception\InvalidArgumentInPropertyComparison;
//use App\Plasticine\Merge\Exception\ObjectsAreNotEquivalent;
//use App\Plasticine\Merge\Exception\UnknownMergeError;
//use App\Plasticine\Merge\Exception\UserDecisionRequired;
//use App\Plasticine\Merge\Model;
//use App\Plasticine\Model\Exception\ModelDuplicatedInDatabase;
//use App\Plasticine\Model\Exception\ModelMissedInDatabase;
//use App\Plasticine\Model\Exception\WrongType;
//use App\Plasticine\Neo4j\Exception\EmptyPropertiesException;
//use App\Plasticine\Neo4j\Exception\NoAccessToDatabaseException;
//use App\Plasticine\Neo4j\Exception\WrongConnectionException;
//use App\Plasticine\Neo4j\Exception\WrongOperatorException;
//use App\Plasticine\Settings\Exception\InvalidSettingName;
//use GraphAware\Neo4j\Client\Exception\Neo4jException;

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
        // Request
        if ($e instanceof WrongRequestInput) {
            return '2.1';
//
//            // Collection
//        } elseif ($e instanceof AddAlreadyExistHashException) {
//            return '3.1';
//        } elseif ($e instanceof AddWrongTypeException) {
//            return '3.2';
//
//            // Item
//        } elseif ($e instanceof KeyIsNotAllowedException) {
//            return '3.3';
//        } elseif ($e instanceof PropertyWithKeyMissedException) {
//            return '3.4';
//
//            // Model
//        } elseif ($e instanceof WrongType) {
//            return '3.5';
//        } elseif ($e instanceof ModelDuplicatedInDatabase) {
//            return '3.6';
//        } elseif ($e instanceof ModelMissedInDatabase) {
//            return '3.7';
//
//            // Neo4j
//        } elseif ($e instanceof NoAccessToDatabaseException) {
//            return '4.1';
//        } elseif ($e instanceof EmptyPropertiesException) {
//            return '4.2';
//        } elseif ($e instanceof WrongConnectionException) {
//            return '4.3';
//        } elseif ($e instanceof WrongOperatorException) {
//            return '4.4';
//        } elseif ($e instanceof Neo4jException) {
//            return '4.5';
//
//            // Files
//        } elseif ($e instanceof FileCanNotBeStored) {
//            return '5.1';
//
//            // Import
//        } elseif ($e instanceof InvalidJsonFormat) {
//            return '6.1';
//        } elseif ($e instanceof MissedPrimaryProperty) {
//            return '6.2';
//
//            // Merge
//        } elseif ($e instanceof InvalidArgumentClass) {
//            return '7.1';
//        } elseif ($e instanceof InvalidArgumentInPropertyComparison) {
//            return '7.2';
//        } elseif ($e instanceof ObjectsAreNotEquivalent) {
//            return '7.3';
//        } elseif ($e instanceof UnknownMergeError) {
//            return '7.4';
//        } elseif ($e instanceof UserDecisionRequired) {
//            return '7.5';
//
//            // Settings
//        } elseif ($e instanceof InvalidSettingName) {
//            return '8.1';
//
//        } else {
//            return '0.0';
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
