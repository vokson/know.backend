<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = [
        'password', 'access_token',
    ];

    public $timestamps = true;
    protected $dateFormat = 'U';
}
