<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOauthFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('expires_in')->nullable()->after('refresh_token');
            $table->integer('token_created')->nullable()->after('expires_in');
            $table->string('token_type')->nullable()->after('token_created');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['expires_in', 'token_created', 'token_type']);
        });
    }
}
