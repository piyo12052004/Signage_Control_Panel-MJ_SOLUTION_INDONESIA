
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('device_id')
                ->constrained('devices')
                ->cascadeOnDelete();

            $table->foreignUuid('content_id')
                ->constrained('contents')
                ->cascadeOnDelete();

            $table->timestamps();

            // Mencegah content yang sama terhubung dua kali
            // ke device yang sama.
            $table->unique(['device_id', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_contents');
    }
};
