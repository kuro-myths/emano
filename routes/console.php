<?php

use Illuminate\Support\Facades\Schedule;

// Penjadwal. Sinkronisasi berkala akun akan ditambahkan setelah integrasi penyedia tersedia.
Schedule::command('queue:prune-failed --hours=168')->daily();
