<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Admin, Librarian, Member
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Users Table (Modifying default)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
        });
        // 3. Member Profiles
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamps();
        });

        // 4. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 5. Books
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('isbn')->unique()->nullable();
            $table->year('published_year')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6. Authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        // Pivot: author_book
        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->primary(['author_id', 'book_id']);
        });

        // 7. Book Copies (Physical tracking)
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->enum('status', ['available', 'borrowed', 'lost', 'damaged', 'maintenance'])->default('available');
            $table->timestamps();
        });

        // 8. Borrowing Transactions
        Schema::create('borrowing_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // The member
            $table->foreignId('book_copy_id')->constrained()->restrictOnDelete();
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue', 'lost'])->default('active');
            $table->timestamps();
        });

        // 9. Borrowing Requests
        Schema::create('borrowing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'fulfilled'])->default('pending');
            $table->timestamps();
        });

        // 10. Fines
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_transaction_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'waived'])->default('unpaid');
            $table->timestamps();
        });

        // 11. Fine Payments
        Schema::create('fine_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        // 12. Library Settings
        Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 13. Notifications (Laravel default notifications table schema)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 14. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('library_settings');
        Schema::dropIfExists('fine_payments');
        Schema::dropIfExists('fines');
        Schema::dropIfExists('borrowing_requests');
        Schema::dropIfExists('borrowing_transactions');
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('books');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('member_profiles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};