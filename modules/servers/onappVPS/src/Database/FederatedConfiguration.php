<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class FederatedConfiguration extends Database
{

    protected $table    = 'onappVPS_FederatedConfiguration';
    protected $builder;
    public    $fillable = ['id', 'template_id', 'active', 'product_id'];


}