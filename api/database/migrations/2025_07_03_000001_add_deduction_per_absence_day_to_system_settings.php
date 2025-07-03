<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->decimal('deduction_per_absence_day', 8, 4)->default(0)->after('id');
        });
    }

    public function down()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('deduction_per_absence_day');
        });
    }
};
