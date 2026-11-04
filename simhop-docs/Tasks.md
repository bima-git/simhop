# TASKS — Status dan Backlog Berurutan SIMHOP

Legenda: [x] selesai dan diuji pemilik · [~] kode ada tetapi belum dikonfirmasi diterapkan/diuji · [ ] belum dikerjakan.

> Agent: kerjakan **satu tugas per sesi**, sesuai urutan. Perbarui berkas ini setelah selesai. Jangan mulai tugas yang dependensinya belum selesai.

## STATUS SAAT INI (per 28 Okt 2026)

### Fondasi (selesai dan diuji pemilik)
- [x] Instalasi Laravel 12 + Breeze (Blade) + Tailwind, MySQL `simhop_db` (Laragon)
- [x] 7 migrasi (users + kolom role/nim/is_active, projects, project_user, tasks, handover_documents, video_submissions, revisions) dibuat dan `php artisan migrate` sukses
- [x] Model: User, Project, Task, HandoverDocument, Revision, VideoSubmission (relasi User-Project many-to-many terbukti di Tinker; `HandoverDocument` dan `Revision` belum diuji langsung)
- [x] Konversi video: `VideoPlatform`, `VideoEmbed`, `VideoEmbedService`, `ValidVideoUrl`, `<x-video-player>`; terbukti di Tinker untuk YouTube dan Drive
- [x] Middleware `role` + alias di `bootstrap/app.php`; `routes/web.php` dengan redirect per role dan grup admin/coach/mahasiswa
- [x] `/register` ditutup (404); login 3 akun mengalihkan ke dasbor yang benar; lintas role 403
- [x] Seeder 4 akun (admin, coach, coach2, mahasiswa)
- [x] Rute manajemen proyek Coach terdaftar (7 rute coach); akses proyek milik coach lain = 403 (terbukti)

### Kode sudah diberikan tetapi belum dikonfirmasi
- [x] Manajemen proyek Coach: tambah/keluarkan anggota, daftar, buat proyek via browser (diuji penuh via `CoachProjectTest`)
- [x] Menu "Proyek" di `navigation.blade.php` (desktop dan mobile)
- [x] `tests/Feature/RoleAccessTest.php` dan `tests/Unit/VideoEmbedServiceTest.php`
- [x] Hapus `tests/Feature/Auth/RegistrationTest.php`

---

## T-00 — Verifikasi dan housekeeping (selesai)
**Tujuan**: pastikan fondasi benar sebelum menambah fitur.
- [x] Hapus `tests/Feature/Auth/RegistrationTest.php` bila masih ada
- [x] Pastikan `RoleAccessTest` dan `VideoEmbedServiceTest` ada; perbarui `UserFactory` agar mendukung `role`, `is_active`; jalankan `php artisan test` sampai hijau (61 passed, 164 assertions)
- [x] Pastikan menu "Proyek" tampil untuk Coach (desktop dan mobile)
- [x] Tulis `CoachProjectTest` (lihat TESTING.md A.2)
- [~] Hapus proyek yatim (created_by null) bila ada (akan dicek saat koneksi MySQL Laragon aktif)
**Selesai bila**: `php artisan test` hijau; alur proyek (buat, tambah/keluarkan anggota) terverifikasi. [SELESAI - 3 Nov 2026]

## T-01 — Tugas sisi Coach (selesai)
**Dependensi**: T-00. **Aturan**: BR-20..BR-26.
**Spesifikasi**:
- [x] `TaskRequest` (`authorize`: Coach pemilik proyek; rules: title required max 255, description nullable, due_date nullable date, assigned_to nullable + exists users role mahasiswa + anggota `project_user` proyek tsb)
- [x] `Coach\TaskController`: `store` (pada `projects/{project}/tasks`), `edit`, `update`, `destroy` dengan cek kepemilikan
- [x] Rute: `coach.projects.tasks.store`, `coach.tasks.edit`, `coach.tasks.update`, `coach.tasks.destroy`
- [x] `ProjectController::show` memuat `tasks.assignee`
- [x] `coach/projects/show.blade.php`: bagian "Tugas" (tabel + form Tambah Tugas); `coach/tasks/edit.blade.php`
- [x] Coach tidak mengubah status/progres lewat form ini
**Selesai bila**: BB-T01..T03 lulus; `CoachTaskTest` hijau; coach lain 403 pada edit/hapus. [SELESAI - 3 Nov 2026]

## T-02 — Dasbor dan tugas Mahasiswa (selesai)
**Dependensi**: T-01. **Aturan**: BR-22..BR-25, BR-52.
- [x] Komponen `x-status-badge` (pending/approved/revision dan todo/in_progress/done)
- [x] `Mahasiswa\DashboardController`: proyek yang diikuti, tugas aktif, ringkasan status video
- [x] `Mahasiswa\TaskController`: `index`, `show`, `progress` (PATCH). Hanya tugas `assigned_to = auth()->id()`
- [x] Form progres: status (`todo`/`in_progress` saja), progress 0-100, progress_note
- [x] Ganti closure `mahasiswa.dashboard` dengan controller
**Selesai bila**: BB-M01..M04 lulus; `MahasiswaTaskTest` hijau; mahasiswa tidak bisa set `done` maupun ubah tugas orang lain. [SELESAI - 3 Nov 2026]

## T-03 — Modul Handover (selesai)
**Dependensi**: T-01. **Aturan**: BR-30..BR-34. **Asumsi A-03**.
- [x] `HandoverDocumentRequest` (authorize: coach pemilik; rules: title, type in TYPES, file max:10240, mimes pdf/zip/txt/md/doc/docx/png/jpg/jpeg)
- [x] `Coach\HandoverController` (index, create, store, edit, update, destroy) + cek kepemilikan `ensureOwner`
- [x] Penyimpanan privat `storage/app/private/handover/{project_id}/`; hapus berkas fisik saat dihapus/diganti
- [x] `HandoverDownloadController` (invokable) dengan otorisasi (Coach pemilik ATAU Mahasiswa anggota)
- [x] `Mahasiswa\ProjectController@show` — mahasiswa melihat dokumen handover dikelompokkan per tipe
- [x] View: `coach/handover/index`, `create`, `edit`; `mahasiswa/projects/show`
- [x] Tombol "Kelola Handover" di `coach/projects/show`; link proyek di dashboard mahasiswa
- [x] `HandoverTest` (13 tes): CRUD, file >10MB ditolak, tipe terlarang ditolak, anggota unduh, non-anggota 403, coach lain 403
**Selesai bila**: BB-H01..H06 lulus; `HandoverTest` hijau. [SELESAI - 4 Nov 2026]

## T-04 — Mahasiswa kirim video (selesai)
**Dependensi**: T-02. **Aturan**: BR-40..BR-45, BR-47, BR-49. **Asumsi A-01, A-02**.
- [x] `VideoSubmissionRequest` (authorize: tugas milik sendiri & belum done, kirim ulang hanya saat status revision; rules: title, description, video_url with ValidVideoUrl)
- [x] `Mahasiswa\VideoSubmissionController@store` (rute `mahasiswa.videos.store`), `@update` (kirim ulang, `mahasiswa.videos.update`)
- [x] Kirim ulang hanya saat `revision` (D-20): menimpa baris yang sama, `video_url` diganti, `embed_url`/`platform` dihitung ulang otomatis, `status = pending`, kosongkan `reviewed_by`/`reviewed_at`, riwayat revisi tetap utuh
- [x] UI `mahasiswa/tasks/show.blade.php`: player video, petunjuk Drive ("Siapa saja yang memiliki link") dan YouTube Unlisted, badge status, riwayat revisi, form kirim awal / form kirim ulang
- [x] `VideoSubmissionTest` (11 tes): valid YouTube/Drive, URL terlarang/malformed ditolak, hanya tugas sendiri, tolak jika tugas done, tolak duplikat store, resubmit saat revision sukses, tolak resubmit saat pending/approved, coach/admin 403
**Selesai bila**: BB-V01..V03, V09, V10 lulus; `VideoSubmissionTest` hijau. [SELESAI - 4 Nov 2026]

## T-05 — Review Coach (inti sistem) (selesai)
**Dependensi**: T-04. **Aturan**: BR-46, BR-48, BR-50, BR-51.
- [x] `Coach\VideoReviewController`: `index` (antrean video dengan filter status pending/revision/approved/all, eager load `task.project`, `user`), `show` (player + riwayat revisi), `approve`, `revise`
- [x] View: `coach/videos/index.blade.php` (tab filter dan antrean), `coach/videos/show.blade.php` (<x-video-player>, tombol Setujui hijau, form Minta Revisi oranye, linimasa riwayat revisi kronologis)
- [x] Menu "Review Video" di navigasi Coach (desktop & mobile) dan link langsung "Review Video" di tabel tugas proyek
- [x] `ReviseVideoRequest`: `note` required, string, max 5000 (tanpa batas minimal, D-19); otorisasi hanya Coach pembuat proyek
- [x] Transaksi atomik `DB::transaction`:
  - `approve`: video status approved, reviewed_by/reviewed_at tercatat, dan `task->markAsDone()` (status done, progress 100%)
  - `revise`: simpan baris `revisions` (`coach_id`, `note`), status video revision, task tetap in_progress
- [x] `VideoReviewTest` (11 tes): index, show, approve (+ task done 100%), tolak approve ulang, revisi tanpa catatan ditolak, revisi dengan catatan sukses, riwayat kronologis, coach lain 403, role lain 403, rollback transaksi atomik jika gagal
**Selesai bila**: BB-V04..V08, V11 lulus; `VideoReviewTest` hijau (termasuk atomisitas). [SELESAI - 4 Nov 2026]

## T-06 — Manajemen pengguna Admin (selesai)
**Dependensi**: T-00. **Aturan**: BR-01..BR-08.
- [x] `Admin\UserController` (index, create, store, edit, update, toggle aktif); FormRequest (`StoreUserRequest`, `UpdateUserRequest` dengan validasi email unique, 3 role, NIM wajib untuk mahasiswa)
- [x] Aturan keras BR-06 ditegakkan: Admin dilarang menonaktifkan akun sendiri (403), dilarang mengubah role akun sendiri (403), dan dilarang menonaktifkan admin terakhir (minimal 1 admin aktif tersisa)
- [x] Password awal diatur Admin (min 8 karakter), pengguna dapat mengubahnya lewat Profil
- [x] Ganti closure `admin.dashboard` dengan `Admin\DashboardController@index` (statistik pengguna per role, akun nonaktif, daftar pengguna terbaru)
- [x] View: `admin/dashboard`, `admin/users/index`, `admin/users/create`, `admin/users/edit`
- [x] Menu "Manajemen Pengguna" di navigasi Admin (desktop & mobile)
- [x] `AdminUserTest` (10 tes): index/dashboard, buat akun tiap role (+login), tolak email duplikat, wajib NIM untuk mahasiswa, edit & reset password, toggle aktif/nonaktif, tolak nonaktifkan diri sendiri, tolak ubah role sendiri, tolak nonaktifkan admin terakhir, non-admin 403
**Selesai bila**: BB-U01..U04 lulus; `AdminUserTest` hijau. [SELESAI - 4 Nov 2026]

## T-07 — Dasbor sungguhan per role (selesai)
**Dependensi**: T-05, T-06.
- [x] Admin: `Admin\DashboardController@index`, metrik pengguna per role, kartu akun nonaktif, daftar 5 pengguna terbaru
- [x] Coach: `Coach\DashboardController@index`, metrik proyek (aktif/selesai), status tugas (todo/in progress/done), jumlah mahasiswa bimbingan, kartu antrean `pending review` video, daftar 5 video pending dengan tombol aksi cepat, daftar proyek terbaru
- [x] Mahasiswa: `Mahasiswa\DashboardController@index`, metrik status video, daftar tugas aktif terurut tenggat waktu, kartu proyek dengan hitungan `📄 X Dokumen Handover` dan tautan langsung ke dokumen handover proyek
- [x] Optimasi query tanpa N+1 (`with()`, `withCount()`, dan `count()`)
- [x] `DashboardTest` (5 tes): data nyata admin, data nyata coach, data nyata mahasiswa, redirect `/dashboard` per role, proteksi 403 lintas role
**Selesai bila**: ketiga dasbor menampilkan data nyata tanpa N+1. [SELESAI - 4 Nov 2026]

## T-08 — Pengerasan (hardening) (selesai)
- [x] Tolak akun nonaktif langsung saat login (`LoginRequest`), menampilkan pesan "Akun Anda dinonaktifkan. Hubungi Admin."
- [x] Nonaktifkan hapus akun lewat Profil (BR-07) di `ProfileController@destroy` (403), perbarui view profil dengan card informasi admin, dan update `ProfileTest`
- [x] Halaman error kustom 403 (`resources/views/errors/403.blade.php`) dan 404 (`resources/views/errors/404.blade.php`) berbahasa Indonesia dengan tombol navigasi
- [x] `ensureOwner` dipertahankan pada controller/FormRequest (sederhana, konsisten, dan mudah dipahami/dipertanggungjawabkan saat sidang TA)
- [x] Soft Deletes (D-12) ditunda sesuai keputusan D-12 (tidak mengubah skema 7 tabel D-01 tanpa kebutuhan mendesak)
- [x] Pengujian hardening: `AuthenticationTest` (inactive login ditolak), `ProfileTest` (hapus akun ditolak 403), `RoleAccessTest` (tampilan halaman error 403 & 404 dalam bahasa Indonesia)
**Selesai bila**: seluruh pengerasan keamanan BR-03, BR-07, dan error handler aktif dan hijau. [SELESAI - 4 Nov 2026]

## T-09 — Kelengkapan tes (selesai)
- [x] Semua kelas tes di TESTING.md A.2 ada dan hijau (`CoachProjectTest`, `CoachTaskTest`, `MahasiswaTaskTest`, `HandoverTest`, `VideoSubmissionTest`, `VideoReviewTest`, `AdminUserTest`, `DashboardTest`, `RoleAccessTest`, `VideoEmbedServiceTest`, dll.)
- [x] Unit test aturan `ValidVideoUrlRuleTest` (5 skenario: YouTube valid, Drive valid, host terlarang, malformed URL, tipe non-string)
- [x] Unit test model `TaskModelTest` (`Task::markAsDone()` mengupdate status `done` & progress 100%, serta verifikasi konstanta status)
**Selesai bila**: seluruh unit dan feature test (142 tes, 490 assertions) hijau tanpa kegagalan. [SELESAI - 4 Nov 2026]

## T-10 — Data demo dan polesan UI (selesai)
- [x] Pemisahan seeder akun (`UserSeeder`) dan seeder demo (`DemoDataSeeder`) yang sepenuhnya idempoten (`firstOrCreate` / aman dijalankan berulang kali tanpa error `Duplicate entry`)
- [x] Data demo realistis mencakup proyek magang PT Teknologi Server Indonesia, anggota pemagang, tugas bertingkat (`todo`, `in_progress`, `done`), dokumen handover (`tech_stack`, `guide`), dan pengiriman video YouTube serta Google Drive berserta review revisi coach
- [x] Halaman utama (`/`) langsung mengalihkan pengunjung tamu ke halaman login (`/login`) dan pengguna yang sudah login ke dasbor (`/dashboard`)
- [x] Polesan UI: layout guest login berlabel identitas SIMHOP, file lokalisasi bahasa Indonesia (`lang/id.json`, `lang/id/auth.php`, `lang/id/passwords.php`), dan responsivitas kartu login
**Selesai bila**: `php artisan db:seed` berjalan lancar tanpa error duplikasi dan antarmuka login siap digunakan. [SELESAI - 4 Nov 2026]

## T-11 — Dokumentasi untuk Bab III/IV (draf selesai disiapkan)
- [x] Dokumen draf pengujian Bab IV lengkap disiapkan di `simhop-docs/DRAFT_PENGUJIAN_BAB_IV.md`
- [x] Tabel pengujian fungsional Black Box lengkap (41 skenario: Autentikasi, Admin, Proyek, Tugas, Handover, Video & Review) yang dipetakan langsung dengan automated test PHPUnit
- [x] Panduan dan instrumen pengujian pengguna (UAT) untuk peran Coach dan Mahasiswa
- [x] Kuesioner evaluasi kegunaan sistem standar *System Usability Scale* (SUS) 10 butir pernyataan berserta formula penilaian dan kategori interpretasi skor
- [x] Daftar panduan tangkapan layar (18 daftar gambar) untuk kebutuhan lampiran dan pembahasan Bab IV
- [ ] Pengisian data pengujian riil di lapangan dan kuesioner pengguna [DIKERJAKAN OLEH PENULIS]
**Status**: Draf instrumen dan tabel pengujian telah lengkap, siap diisi hasil nyata oleh penulis. [4 Nov 2026]

---

## Backlog gagasan (JANGAN dikerjakan tanpa persetujuan)
- Parameter waktu mulai video (`t=`)
- Ekspor laporan progres (PDF/Excel)
- Filter/pencarian tugas dan video