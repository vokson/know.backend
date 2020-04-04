<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FeedbackController as Feedback;

class TestController extends Controller
{
    public function do(Request $request)
    {
        return Feedback::success([
            'response' => 'OK'
        ]);
    }
}
