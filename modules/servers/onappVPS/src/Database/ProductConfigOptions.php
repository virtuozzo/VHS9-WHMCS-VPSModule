<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class ProductConfigOptions extends Database
{

    protected $table    = 'tblproductconfigoptions';
    protected $builder;
    public    $fillable = ['id', 'gid', 'optionname', 'optiontype', 'qtyminimum', 'qtymaximum', 'order', 'hidden'];
    public $timestamps = false;

    public function save(array $options = array())
    {
        $pco = new ProductConfigOptions();
        $query = $pco->where('optionname', $this->optionname)->where('gid', $this->gid);
        if($query->count() > 0) {
            $this->fill($query->first()->toArray());
            return false;
        }

        return parent::save($options);
    }

}