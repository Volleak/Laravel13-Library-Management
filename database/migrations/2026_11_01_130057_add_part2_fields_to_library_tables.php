<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('address');
            $table->string('photo_path')->nullable()->after('dob');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->string('publisher')->nullable()->after('published_year');
            $table->string('cover_path')->nullable()->after('publisher');
            $table->string('shelf_location')->nullable()->after('cover_path');
        });

        Schema::table('fine_payments', function (Blueprint $table) {
            $table->foreignId('processed_by_id')->nullable()->constrained('users')->nullOnDelete()->after('payment_method');
            $table->string('reference_number')->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropColumn(['dob', 'photo_path']);
        });
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['publisher', 'cover_path', 'shelf_location']);
        });
        Schema::table('fine_payments', function (Blueprint $table) {
            $table->dropForeign(['processed_by_id']);
            $table->dropColumn(['processed_by_id', 'reference_number']);
        });
    }
};