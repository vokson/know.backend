<?php

namespace App\Http\Controllers;

use App\Log;
use Carbon\Carbon;
use App\ArticleFile;

class ServiceController extends Controller
{
    public function getDatabaseBackup()
    {
        $headers = array(
            'Content-Type' => 'application/octet-stream',
            'Access-Control-Expose-Headers' => 'Content-Filename',
            'Content-Filename' => rawurlencode(Carbon::now()->toDateTimeString() . ".sqlite"),
        );

        return response()->download(config('database.connections.sqlite.database'), "", $headers);
    }



    public function info()
    {
        phpinfo();
        return;
    }

    public static function createFolderForFileByNumber($number, $pitch = 1000)
    {
        $min = intdiv($number, $pitch) * $pitch + 1;
        $max = (intdiv($number, $pitch) + 1) * $pitch;

        if ($number % $pitch == 0) {
            $min = $min - $pitch;
            $max = $max - $pitch;
        }

//        echo "MIN = " . $min . '<br/>';
//        echo "MAX = " . $max . '<br/>';

        return sprintf("%05d-%05d", $min, $max);
    }

}
