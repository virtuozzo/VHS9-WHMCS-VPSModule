<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class ProductConfigLinks extends Database
{

    protected $table    = 'tblproductconfiglinks';
    protected $builder;
    public    $fillable = ['gid', 'pid'];
    public $timestamps = false;

}