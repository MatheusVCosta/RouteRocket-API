<?php

use App\Models\Redirect;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redirect_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('redirect_id')->nullable(true)->index();
            $table->ipAddress('ip_address_request')->nullable(true);
            $table->string('user_agent')->nullable(true);
            $table->string('header_referer')->nullable(true);
            $table->string('query_params')->nullable(true);
            $table->datetime('last_access_at');

            $table->foreign('redirect_id')->references('id')->on('redirects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redirect_logs');
    }
};
