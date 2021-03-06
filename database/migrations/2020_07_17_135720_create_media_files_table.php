<?php

use App\Models\MediaFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('media_id');

            $table->string('extension')->nullable();
            $table->string('downloader');
            $table->string('name');

            $table->enum('status', [
                MediaFile::STATUS_INITIAL, 
                MediaFile::STATUS_STARTED,
                MediaFile::STATUS_SUCCESS,
                MediaFile::STATUS_BROKEN,
            ]);

            $table->dateTime('started_at')->nullable();
            $table->dateTime('broken_at')->nullable();
            $table->dateTime('success_at')->nullable();

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
        Schema::dropIfExists('media_files');
    }
}
