<?php

/* * ********************************************************************
 * proxmoxCloud product developed. (2016-12-29)
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
namespace onappWrapper\migrations;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;
use onappWrapper\models\User;

/**
 * Description of TableSchema
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class TableSchema {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        if (DB::schema()->hasTable('mg_onapp_users') === false) {
                DB::schema()->table('mg_onapp_users', function (Blueprint $table) {
                    $table->create();
                    $table->increments('id');
                    $table->integer('user_id');
                    $table->integer('hosting_id')->nullable()->default(0);
                    $table->integer('onapp_id')->default(0);
                    $table->string('username')->notnull();//unique
                    $table->string('email');
                    $table->string('password');
                    $table->string('api_key');
                    $table->timestamps();
                });
        }
        return $this;
    }
    
    public function upgrade(){
        if (DB::schema()->hasTable('onappVPS_auth') === false) {
            return $this;
        }
        /**
         * Version 1.7.3
         */
        $statement = DB::connection()
                   ->getPdo()
                   ->prepare("SELECT vps.* FROM `onappVPS_auth` vps WHERE vps.user_id NOT IN (SELECT u.user_id FROM mg_onapp_users u)");
        $statement->execute();
        while($row= $statement->fetch(\PDO::FETCH_ASSOC)){
            $row['key'] = decrypt($row['key']);
            $user = new User();
            $user->setUserIdAttribute($row['user_id'])
                 ->setHostingIdAttribute(0)
                 ->setOnappIdAttribute(0)
                 ->setUsernameAttribute($row['username'])
                 ->setEmailAttribute($row['email'])
                 ->setPasswordAttribute('')
                 ->setApiKeyAttribute($row['key']);
            $user->save();
        }
        return $this;
    }
}
