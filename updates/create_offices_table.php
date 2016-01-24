<?php namespace GreenImp\Offices\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOfficesTable extends Migration
{

    public function up()
    {
        Schema::create('greenimp_offices_offices', function($table)
        {
          $table->engine = 'InnoDB';
          $table->increments('id');
          $table->integer('country_id')->unsigned();
          $table->string('name');
          $table->string('url_slug', 255);
          $table->string('image', 2000)->nullable();
          $table->longText('description')->nullable();
          $table->string('location')->nullable();
          $table->longText('address')->nullable();
          $table->string('city')->nullable();
          $table->string('zip')->nullable();
          $table->integer('state_id')->unsigned()->nullable();
          $table->decimal('latitude', 10, 8);
          $table->decimal('longitude', 10, 8);
          $table->integer('group_id')->unsigned();
          $table->boolean('active')->default(false);
          $table->timestamps();

          /**
           * Indexes
           */
          $table->index('active');
          $table->index('country_id');

          /**
           * Foreign keys
           */
          $table->foreign('group_id')
                ->references('id')
                ->on('greenimp_offices_groups')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('greenimp_offices_offices');
    }

}
