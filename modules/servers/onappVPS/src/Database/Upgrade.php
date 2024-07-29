<?php

/* * ********************************************************************
 * onapp product developed. (2016-11-17)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

namespace OnAppVps\Database;

/**
 * Description of Upgrade
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class Upgrade extends \Illuminate\Database\Eloquent\Model{
    
    protected $table    = 'tblupgrades';
    protected $builder;
    public    $fillable = ['id', 'name', 'type', 'relid', 'originalvalue', 'newvalue', 'amount','recurringchange','status', 'paid'];
    public $timestamps = false;
    
}
