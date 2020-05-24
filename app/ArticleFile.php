<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleFile extends Model
{
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_files';

}
