<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // フィールド定義
            $table->string('field_name'); // 内部名（英数字）
            $table->string('field_label'); // 表示名
            $table->string('field_type'); // text, textarea, number, date, select, checkbox
            $table->json('field_options')->nullable(); // selectの選択肢など
            $table->string('target_model'); // student_profile, guardian
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['tenant_id', 'target_model', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_custom_fields');
    }
};
