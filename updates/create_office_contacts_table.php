<?php namespace GreenImp\Offices\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOfficeContactsTable extends Migration
{

    public function up()
    {
        Schema::create('greenimp_offices_contacts', function($table)
        {
          $table->engine = 'InnoDB';
          $table->increments('id');
          $table->integer('office_id')->unsigned();
          $table->enum('type', ['tel', 'fax', 'email', 'other']);
          $table->string('value');
          $table->string('label')->nullable();
          $table->timestamps();

          /**
           * Foreign keys
           */
          $table->foreign('office_id')
                ->references('id')
                ->on('greenimp_offices_offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('greenimp_offices_contacts');
    }

}
