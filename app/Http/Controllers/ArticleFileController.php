<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use App\Exceptions\ArticleFile\Download\MissedFileInDatabase;
use App\Exceptions\ArticleFile\Download\MissedFileInStorage;
use App\Exceptions\ArticleFile\Upload\FileCanNotBeStored;
use App\Exceptions\ArticleFile\Upload\InvalidFile;
use App\Exceptions\ArticleFile\Upload\MissedFile;
use App\Exceptions\ArticleFile\Upload\NullArticle;
use App\Exceptions\ArticleFile\Validation\Id;
use App\Exceptions\ArticleFile\Validation\Uin;
use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController As Feedback;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ZipArchiveController;

class ArticleFileController extends Controller
{

    public static function validateId($value)
    {
        throw_if(
            !((ctype_digit($value) || is_int($value)) && $value > 0),
            new Id()
        );

        return true;
    }

    public static function validateString($value)
    {
        return (!is_null($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    public static function validateUin($value)
    {
        throw_if(!self::validateString($value), new Uin());

        return true;
    }

    public function upload(Request $request)
    {
        // Получаем ID статьи
        $article_id = $request->input('article_id', null);
        self::validateId($article_id);

        $article = Article::where('uin', $article_id)->first();
        throw_if(is_null($article), new NullArticle());

        // Получаем UIN загружаемого файла
        $uin = $request->input('uin', null);
        self::validateUin($uin);

        // Проверяем есть ли файл
        throw_if(!$request->hasFile('file'), new MissedFile());
        throw_if(!$request->file('file')->isValid(), new InvalidFile());

        $way = 'ARTICLE_FILES' .
            DIRECTORY_SEPARATOR .
            $this->createFolderByNumber($article_id) .
            DIRECTORY_SEPARATOR .
            $article_id;

//        return Feedback::success([
//            'way' => $way,
//            'uin' => $uin,
//            'article_id' => $article_id
//        ]);

        try {
            $path = Storage::putFile($way, $request->file('file'));
        } catch (\Exception $e) {
            throw new FileCanNotBeStored();
        }

        throw_if($path === false, new FileCanNotBeStored());

        $file = new ArticleFile();
        $file->original_name = $request->file('file')->getClientOriginalName();
        $file->size = $request->file('file')->getSize();
        $file->article_id = $article_id;
        $file->server_name = $path;
        $file->uin = $request->input('uin');
        $file->save();

        // Изменяем is_attachment_exist для записи
        Article::where('uin', $article_id)->update(['is_attachment_exist' => true]);

        return Feedback::success([
            'id' => $file->id,
            'uin' => $file->uin,
            'article_id' => $file->article_id
        ]);
    }

    protected function createFolderByNumber($number)
    {
        $min = intdiv($number, 1000) * 1000 + 1;
        $max = (intdiv($number, 1000) + 1) * 1000;

        if ($number % 1000 == 0) {
            $min = $min - 1000;
            $max = $max - 1000;
        }

        return sprintf("%05d-%05d", $min, $max);
    }

    public function get(Request $request)
    {
        // Получаем ID статьи
        $article_id = $request->input('article_id', null);
        self::validateId($article_id);

        $items = ArticleFile::where('article_id', $article_id)
            ->select('id', 'uin', 'original_name', 'size')
            ->orderBy('id', 'asc')
            ->get();

        return Feedback::success([
            'items' => $items->toArray(),
        ]);
    }

//    public function delete(Request $request)
//    {
//        $file_id = null;
//        if (Input::has('id')) {
//
//            if (!ArticleFile::where('id', '=', Input::get('id'))->exists()) {
//                return Feedback::getFeedback(604);
//            } else {
//                $file_id = $request->input('id');
//            }
//        }
//
//        $file = ArticleFile::find($file_id);
//
//        try {
//
//            Storage::delete($file->server_name);
//
//        } catch (QueryException $e) {
//
//            return Feedback::getFeedback(603);
//        }
//
//        $uin = $file->uin;
//        $log_id = $file->log;
//
//        // Удаляем файл
//        $file->delete();
//
//        // Проверяем есть ли еще файлы у данной записи
//        // Если нет, изменяем is_attachment_exist для записи
//        if (ArticleFile::where('log', $log_id)->count() <= 0) {
//            $log = Log::find($log_id);
//            $log->is_attachment_exist = false;
//            $log->save();
//        }
//
//        return Feedback::getFeedback(0, [
////            'log_id' => $log_id,
//            'uin' => $uin
//        ]);
//    }

    public function download(Request $request)
    {
        $file_id = $request->input('file_id', null);
        self::validateId($file_id);

        $file = ArticleFile::find($file_id);
        throw_if(is_null($file), new MissedFileInDatabase());

        $path = storage_path('app' . DIRECTORY_SEPARATOR . $file->server_name);
        throw_if(!file_exists($path), new MissedFileInStorage());

        $headers = array(
            'Content-Type' => 'application/octet-stream',
            'Access-Control-Expose-Headers' => 'Content-Filename',
            'Content-Filename' => rawurlencode($file->original_name)
        );

        return response()->download($path, "", $headers);
    }

//    public function downloadAll(Request $request)
//    {
//        $log_id = intval(Input::get('id', 0));
//        $files = ArticleFile::where('log', $log_id)->get();
//
//        $filesForZipArchive = [];
//        foreach ($files as $file) {
//            $filesForZipArchive[] = [
//                'absolute_path' => storage_path("app/" . $file->server_name),
//                'filename' => $file->original_name
//            ];
//        }
//
//        return ZipArchiveController::download($filesForZipArchive);
//    }

}
