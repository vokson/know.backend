<?php

namespace App;

use App\Http\Controllers\ActionController;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = [
        'password', 'access_token',
    ];

    public $timestamps = true;
    protected $dateFormat = 'U';

    public function mayDo(string $nameOfAction)
    {
        return ActionController::take($this->role, $nameOfAction);
    }
}
