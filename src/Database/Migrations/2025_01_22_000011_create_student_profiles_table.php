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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // 基本情報
            $table->string('student_number')->unique(); // 生徒番号
            $table->string('grade')->nullable(); // 学年
            $table->string('school_name')->nullable(); // 学校名
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // male, female, other, prefer_not_to_say

            // 緊急連絡先
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // 健康情報
            $table->text('medical_notes')->nullable(); // アレルギー、持病など
            $table->string('blood_type')->nullable();

            // 在籍情報
            $table->date('joined_date')->nullable(); // 入会日
            $table->date('withdrawal_date')->nullable(); // 退会日
            $table->string('status')->default('active'); // active, inactive, graduated, withdrawn

            // カスタムフィールド（JSON）
            $table->json('custom_fields')->nullable();

            // 管理者メモ
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index('student_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
