<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catatan: kolom tambahan sudah digabungkan langsung ke create_users_table.
// File ini sengaja dikosongkan agar tidak duplikasi kolom.
return new class extends Migration
{
    public function up(): void
    {
        // Sudah digabungkan ke 0001_01_01_000000_create_users_table.php
    }

    public function down(): void
    {
        // Tidak ada yang perlu di-rollback di sini
    }
};
