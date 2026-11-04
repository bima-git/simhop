# GUIDELINES — Konvensi Kode dan UI SIMHOP

Ikuti pola kode yang **sudah ada** di codebase. Jangan memperkenalkan gaya, pustaka, atau arsitektur baru tanpa izin.

## 1. PHP / Laravel 12

- PSR-12, `declare(strict_types=1);` dipakai pada berkas Enum/Service/Support/Rule (sudah begitu). Controller dan model bawaan Laravel boleh tanpa.
- Laravel 12: middleware dikonfigurasi di `bootstrap/app.php` (tidak ada `Kernel.php`). Model memakai method `casts()`, bukan properti `$casts`.
- Nama kelas Inggris (PascalCase), nama method camelCase, nama tabel/kolom snake_case plural/singular sesuai [DATABASE.md](DATABASE.md).
- Konstanta untuk nilai enum yang sering dipakai (mis. `Task::STATUS_DONE`, `User::ROLE_COACH`). Jangan menyebar string literal `'done'` di banyak tempat.
- **Controller tipis**: validasi di FormRequest, logika di model/service, controller hanya mengoordinasikan.
- Satu FormRequest per form (`Store...Request`, `TaskRequest`, dst.). `authorize()` memeriksa kepemilikan bila relevan.
- **Dilarang**: `$request->all()`, query mentah tanpa binding, `env()` di luar file config, `dd()`/`dump()` yang tertinggal.
- Multi-tabel = `DB::transaction(function () { ... })`.
- Hindari N+1: gunakan `with()`, `withCount()`.
- Penamaan rute: `{role}.{sumber}.{aksi}` (mis. `coach.projects.members.store`). Gunakan `route('nama')`, bukan URL tertulis.
- Relasi dan akses: gunakan relasi Eloquent (`$project->tasks()`), bukan join manual, kecuali perlu performa.
- Pesan error validasi dalam Bahasa Indonesia (di FormRequest `messages()` atau berkas bahasa).

## 2. Pola otorisasi (jangan diganti tanpa izin)

- Rute: `->middleware(['auth', 'role:coach'])`.
- Kepemilikan: `abort_unless($project->created_by === auth()->id(), 403)` (helper `ensureOwner`) atau `FormRequest::authorize()`.
- Refactor ke Laravel Policy dijadwalkan di T-08. Sebelum itu **jangan** membuat Policy baru agar pola tetap seragam.

## 3. Pola status dan transaksi (inti bisnis)

```php
// Setujui video (BR-46)
DB::transaction(function () use ($video, $coach) {
    $video->update(['status' => 'approved', 'reviewed_by' => $coach->id, 'reviewed_at' => now()]);
    $video->task->markAsDone();
});

// Minta revisi (BR-48): catatan wajib divalidasi di FormRequest
DB::transaction(function () use ($video, $coach, $note) {
    $video->revisions()->create(['coach_id' => $coach->id, 'note' => $note]);
    $video->update(['status' => 'revision', 'reviewed_by' => $coach->id, 'reviewed_at' => now()]);
});
```

## 4. Blade dan Tailwind

- Semua halaman terautentikasi memakai `<x-app-layout>` dengan `<x-slot name="header">`.
- Pakai komponen Breeze: `x-input-label`, `x-text-input`, `x-input-error`, `x-primary-button`, `x-nav-link`. Textarea/select memakai kelas yang sama dengan contoh di `coach/projects/create.blade.php`.
- Dukungan dark mode mengikuti pola kelas `dark:` yang sudah dipakai.
- Output selalu `{{ }}`. Jangan `{!! !!}` untuk input pengguna.
- Form: `@csrf`, `@method('PUT'|'PATCH'|'DELETE')`, `old()` untuk mengisi ulang, `@error`/`x-input-error`.
- Aksi hapus selalu diberi konfirmasi (`onclick="return confirm('...')"`).
- Banner flash: `@if (session('status'))` hijau di bagian atas konten.
- **Badge status** (komponen `x-status-badge`, dibuat di T-02): `pending` kuning, `approved` hijau, `revision` oranye; tugas `todo` abu, `in_progress` biru, `done` hijau.
- Tabel daftar: kolom jelas, baris kosong "Belum ada ...", paginasi `{{ $items->links() }}`.
- Video diputar **hanya** lewat `<x-video-player :submission="$video" />`.

## 5. Bahasa dan teks

- **UI, pesan validasi, flash message, dan komentar kode: Bahasa Indonesia** formal-santai (mis. "Proyek berhasil dibuat.").
- Identifier kode (kelas, method, variabel, kolom) tetap Inggris.
- Istilah asing (*handover*, *tech stack*, *embedded player*) boleh dipakai di UI bila sudah umum bagi pengguna.

## 6. Berkas, unggahan, dan keamanan

- Unggahan: `->store('handover/'.$project->id, 'local')`; validasi `file|max:10240|mimes:...`.
- Unduhan lewat controller dengan otorisasi; jangan memberi URL publik langsung.
- Jangan mempercayai nama berkas dari klien; simpan `getClientOriginalName()` hanya untuk tampilan.
- Tidak menyimpan rahasia di repo. Password seeder dev saja.

## 7. Tes

- Satu kelas tes fitur per modul (`CoachProjectTest`, `CoachTaskTest`, ...) di `tests/Feature`.
- Gunakan `RefreshDatabase`, factory (`User::factory()->create(['role' => 'coach'])`), dan assert status HTTP, redirect, serta isi database.
- Untuk setiap fitur: jalur sukses, validasi gagal, akses lintas role (403), akses milik coach lain (403), tamu (redirect login).
- Sebelum menyatakan selesai: `php artisan test` harus lulus.

## 8. Git dan commit (bila repo dipakai)

- Satu commit per tugas kecil. Pesan commit Inggris atau Indonesia, singkat, bentuk perintah: `feat(coach): tambah CRUD tugas`, `fix(video): tolak tautan Drive folder`, `test: ...`, `docs: ...`.
- Jangan commit `.env`, `vendor/`, `node_modules/`, `storage/*.key`.

## 9. Hal yang bukan gaya bebas (jangan dilakukan)

- Menambah library JS/PHP baru, mengganti Tailwind/Breeze, menambah SPA (Vue/React/Livewire).
- Membuat tabel/kolom baru tanpa DECISIONS.
- Menambahkan fitur di luar `simhop-docs/PRD.md` ("sekalian saja") sebelum fitur wajib selesai.