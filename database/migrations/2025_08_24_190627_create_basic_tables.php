<?php

use App\Models\User;
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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 8)->unique();
            $table->dateTime('deadline')->nullable();
            $table->foreignIdFor(User::class, 'author')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->boolean('isClosed')->default(false);
            $table->string('color', 32)->nullable();
            $table->string('icon', 64)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->foreignId('code')
                ->constrained('assignments')
                ->cascadeOnDelete();

            $table->string('original_filename');
            $table->string('storage_path');                    // e.g. 'submissions/xyz.pdf'
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('mime_type', 128)->nullable();
            $table->string('checksum', 64)->nullable();        // sha256 or md5

            $table->dateTime('submitted_at')->useCurrent();
            $table->timestampsTz();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('submissions');
    }
};
