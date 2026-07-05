<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('issue_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('issue_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('remark')->nullable();

            $table->timestamp('return_date')->nullable(); // 🔥 single datetime

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('issue_returns');
    }
};
