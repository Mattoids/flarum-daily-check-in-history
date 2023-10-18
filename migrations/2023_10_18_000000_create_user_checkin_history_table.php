<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'user_checkin_history',
    function (Blueprint $table) {
        $table->increments('id');

        $table->integer('user_id')->index();
        $table->tinyInteger('type')->default(0)->comment("签到类型： 0-签到 1-补签");
        $table->date('last_checkin_date') -> comment("签到日期")->index();
        $table->integer('total_checkin_count')->comment("总签到次数");
        $table->integer('total_continuous_checkin_count')->comment("连续签到次数");
        $table->dateTime('last_checkin_time') -> comment("签到时间");
    }
);

