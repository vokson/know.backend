<?php

namespace App\Http\Controllers;

use App\Exceptions\Zip\Create\CanNotBeCreated;
use ZipArchive;
use App\Http\Controllers\SettingController as Settings;
use App\Http\Controllers\FeedbackController as Feedback;

class ZipArchiveController extends Controller
{
    // $fileForZipArchive - Array with fields "absolute_path" and "filename"
    public static function download($fileForZipArchive)
    {
        self::cleanOldArchives();

        $archiveName = uniqid() . '.zip';

        $zipPath = config('filesystems.archiveStoragePath') . DIRECTORY_SEPARATOR . $archiveName;

        return $zipPath;

        set_time_limit(Settings::take('ARCHIVE_CREATION_TIME'));

        throw_if(self::createArchive($fileForZipArchive, $zipPath) === FALSE, new CanNotBeCreated());

        $headers = array(
            'Content-Type' => 'application/octet-stream',
            'Access-Control-Expose-Headers' => 'Content-Filename',
            'Content-Filename' => $archiveName
        );

        return response()->download($zipPath, "", $headers);
    }


    public static function createArchive($files, $zipPath)
    {

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZIPARCHIVE::CREATE) === TRUE) {

            foreach ($files as $file) {
                if (file_exists($file['absolute_path'])) {
                    $zip->addFile($file['absolute_path'], $file['filename']);
                }
            }

            if ($zip->numFiles == 0) return FALSE;

            return ($zip->status == ZipArchive::ER_OK);
        }

        return FALSE;
    }

    public static function cleanOldArchives()
    {
        foreach (glob(config('filesystems.archiveStoragePath') . DIRECTORY_SEPARATOR . '*') as $fileName) {
            if ((microtime(true) - filectime($fileName) > Settings::take('ARCHIVE_STORAGE_TIME'))) {
                unlink($fileName);
            }
        }
    }

}
