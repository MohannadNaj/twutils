<?php

use App\Models\Media;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('tweet_id');

            $table->text('raw')->nullable();
            $table->string('type');

            $table->enum('status', [
                Media::STATUS_INITIAL, 
                Media::STATUS_STARTED,
                Media::STATUS_SUCCESS,
                Media::STATUS_BROKEN,
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
        Schema::dropIfExists('media');
    }
}
