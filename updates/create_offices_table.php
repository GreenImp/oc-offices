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
          $table->string('country_code', 2);
          $table->string('name');
          $table->string('image', 2000)->nullable();
          $table->longText('description')->nullable();
          $table->longText('address')->nullable();
          $table->integer('group_id')->unsigned();
          $table->boolean('active')->default(false);
          $table->timestamps();

          /**
           * Indexes
           */
          $table->index('active');

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
