<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class ProductConfigOptionsSub extends Database
{

    protected $table    = 'tblproductconfigoptionssub';
    protected $builder;
    public    $fillable = ['id', 'configid', 'optionname', 'sortorder', 'hidden'];
    public $timestamps = false;

}