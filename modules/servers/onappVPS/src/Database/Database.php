<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class Database extends Model
{

    /**
     * onappVPS_Federated constructor.
     *
     */
    public function __construct()
    {
        $this->runMigrations();
    }

    public function runMigrations()
    {
        try {
            if (Schema::schema()->hasTable('onappVPS_FederatedTemplates') === false) {
                Schema::schema()->table('onappVPS_FederatedTemplates', function (Blueprint $table) {
                    $table->create();
                    $table->increments('id');
                    $table->string('label');
                    $table->integer('onapp_id');
                    $table->string('group');
                    $table->integer('location_id')->nullable();
                    $table->integer('hypervisor_group_id')->nullable();
                    $table->timestamps();
                });
            }
            if (Schema::schema()->hasTable('onappVPS_LocationGroups') === false) {
                Schema::schema()->table('onappVPS_LocationGroups', function (Blueprint $table) {
                    $table->create();
                    $table->increments('id');
                    $table->string('city');
                    $table->string('country');
                    $table->integer('location_id');
                    $table->boolean('federated');
                    $table->string('latlng');
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}