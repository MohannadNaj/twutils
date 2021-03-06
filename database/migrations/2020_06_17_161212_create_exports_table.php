<?php

use App\Models\Export;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks', 'id')->onDelete('cascade');

            $table->enum('type', [
                Export::TYPE_HTML,
                Export::TYPE_EXCEL,
                Export::TYPE_HTMLENTITIES,
            ]);

            $table->enum('status', [
                Export::STATUS_INITIAL, 
                Export::STATUS_STARTED,
                Export::STATUS_SUCCESS,
                Export::STATUS_BROKEN,
            ]);

            $table->string('filename')->nullable();
            $table->unsignedInteger('size')->nullable();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('broken_at')->nullable();
            $table->dateTime('success_at')->nullable();

            $table->unsignedInteger('progress')->nullable();
            $table->unsignedInteger('progress_end')->nullable();

            $table->text('exception')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exports');
    }
}
