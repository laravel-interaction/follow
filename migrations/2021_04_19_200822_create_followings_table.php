<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            config('follow.table_names.followings'),
            function (Blueprint $table): void {
                config('follow.uuids') ? $table->uuid('uuid') : $table->bigIncrements('id');
                $table->unsignedBigInteger(config('follow.column_names.user_foreign_key'))->index()->comment('user_id');
                $table->morphs('followable');
                $table->timestamps();
                $table->unique([config('follow.column_names.user_foreign_key'), 'followable_type', 'followable_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('follow.table_names.followings'));
    }
}
