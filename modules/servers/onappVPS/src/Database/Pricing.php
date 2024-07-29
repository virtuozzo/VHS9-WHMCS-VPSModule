<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class Pricing extends Database
{

    const PRICING_TYPE_PRODUCT        = 'product';
    const PRICING_TYPE_ADDON          = 'addon';
    const PRICING_TYPE_CONFIGOPTIONS  = 'configoptions';
    const PRICING_TYPE_DOMAINREGISTER = 'domainregister';
    const PRICING_TYPE_DOMAINTRANSFER = 'domaintransfer';
    const PRICING_TYPE_DOMAINRENEW    = 'domainrenew';
    const PRICING_TYPE_DOMAINADDONS   = 'domainaddons';

    protected $table      = 'tblpricing';
    protected $builder;
    public    $fillable   = [
        'id',
        'type',
        'currency',
        'relid',
        'msetupfee',
        'qsetupfee',
        'ssetupfee',
        'asetupfee',
        'bsetupfee',
        'tsetupfee',
        'monthly',
        'quarterly',
        'semiannually',
        'annually',
        'biennally',
        'triennally',
    ];
    public    $timestamps = false;

}