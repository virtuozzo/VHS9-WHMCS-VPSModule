<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class ProductConfigGroups extends Database
{

    protected $table    = 'tblproductconfiggroups';
    protected $builder;
    public    $fillable = ['id', 'name', 'description'];
    public $timestamps = false;

}