<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class Currencies extends Database
{

    protected $table    = 'tblcurrencies';
    protected $builder;
    public    $fillable = ['id', 'code', 'prefix', 'suffix', 'format', 'rate', 'default'];
    public $timestamps = false;

}