<?php namespace GreenImp\Offices\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOfficeGroupsTable extends Migration
{

    public function up()
    {
        Schema::create('greenimp_offices_groups', function($table)
        {
          $table->engine = 'InnoDB';
          $table->increments('id');
          $table->string('name');
          $table->string('url_slug', 255);
          $table->longText('description')->nullable();
          $table->integer('sort_order')->unsigned()->nullable();
          $table->boolean('active')->default(false);
          $table->timestamps();

          /**
           * Indexes
           */
          $table->unique('url_slug');
          $table->index('sort_order');
          $table->index('active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('greenimp_offices_groups');
    }

}
