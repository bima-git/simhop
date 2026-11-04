# ARCHITECTURE — Arsitektur Teknis SIMHOP

## 1. Tech stack (tetap, jangan diganti)

| Lapisan | Pilihan |
|---|---|
| Bahasa | PHP 8.4 |
| Framework | Laravel 12 (MVC) |
| Database | MySQL (Laragon) |
| Auth | Laravel Breeze (stack Blade), tanpa registrasi publik |
| Frontend | Blade + Tailwind CSS (Vite), komponen Breeze |
| Hak akses | Middleware kustom `role` (multi-role) |
| Video | Hibrida: hanya tautan + embed iframe (YouTube/Google Drive) |
| Lingkungan dev | Windows + Laragon + `php artisan serve` + `npm run dev` |
| Pengujian | PHPUnit/Pest (bawaan), black box testing manual untuk Bab IV |

## 2. Struktur folder (dengan status)

```
app/
├── Enums/
│   └── VideoPlatform.php            [ADA] youtube | google_drive
├── Http/
│   ├── Controllers/
│   │   ├── Coach/
│   │   │   ├── ProjectController.php   [ADA]
│   │   │   ├── TaskController.php      [T-01]
│   │   │   ├── HandoverController.php  [T-03]
│   │   │   └── VideoReviewController.php [T-05]
│   │   ├── Mahasiswa/
│   │   │   ├── DashboardController.php [T-02]
│   │   │   ├── TaskController.php      [T-02]
│   │   │   └── VideoSubmissionController.php [T-04]
│   │   ├── Admin/
│   │   │   └── UserController.php      [T-06]
│   │   └── HandoverDownloadController.php [T-03]
│   ├── Middleware/
│   │   └── EnsureUserHasRole.php       [ADA] alias 'role'
│   └── Requests/
│       ├── StoreProjectRequest.php     [ADA]
│       ├── TaskRequest.php             [T-01]
│       └── ...                         (satu FormRequest per form)
├── Models/                            [ADA] User, Project, Task, HandoverDocument, VideoSubmission, Revision
├── Rules/
│   └── ValidVideoUrl.php               [ADA]
├── Services/
│   └── VideoEmbedService.php           [ADA]
└── Support/
    └── VideoEmbed.php                  [ADA] readonly value object
resources/views/
├── components/video-player.blade.php   [ADA]
├── components/status-badge.blade.php   [T-02]
├── coach/{projects,tasks,handover,videos}/...
├── mahasiswa/...
├── admin/users/...
└── dashboard.blade.php                 (placeholder bawaan Breeze, diganti per role di T-07)
routes/web.php                          [ADA] grup admin/coach/mahasiswa
bootstrap/app.php                       [ADA] alias middleware 'role'
database/{migrations,seeders}/
tests/{Feature,Unit}/
```

`app/Http/` hanya untuk hal terkait HTTP (controller, middleware, request). Enums, Rules, Services, Support, Models berada langsung di `app/`.

## 3. Alur permintaan

```
Browser -> routes/web.php -> middleware (auth, role:X)
       -> FormRequest (validasi + authorize kepemilikan)
       -> Controller (tipis) -> Model/Service (logika) -> DB
       -> redirect dengan flash 'status' | view Blade
```

- **Controller tipis**: terima request tervalidasi, panggil model/service, kembalikan respons.
- **Logika bisnis** yang memengaruhi lebih dari satu tabel memakai `DB::transaction`.
- **Pesan flash** memakai kunci `status`, ditampilkan sebagai banner hijau.

## 4. Autentikasi dan otorisasi

- Login via Breeze. `routes/auth.php` **tanpa** rute register.
- Alias middleware didaftarkan di `bootstrap/app.php` (gaya Laravel 12, tanpa `Kernel.php`):
  ```php
  $middleware->alias(['role' => \App\Http\Middleware\EnsureUserHasRole::class]);
  ```
- `EnsureUserHasRole`: tamu -> redirect `login`; akun nonaktif -> logout + invalidate + redirect `login` dengan error; role tidak cocok -> `abort(403)`.
- Rute `dashboard` dipertahankan (tujuan redirect Breeze setelah login) dan mengalihkan ke `{role}.dashboard`.
- Pemeriksaan kepemilikan: `FormRequest::authorize()` atau `abort_unless(...)` di controller (pola `ensureOwner`). Jangan hanya mengandalkan UI.

## 5. Peta rute

### Sudah ada
| Metode | URI | Nama | Keterangan |
|---|---|---|---|
| GET | `/` | - | welcome |
| GET | `/dashboard` | `dashboard` | redirect per role |
| * | `/profile` | `profile.*` | profil Breeze (hapus akun akan dinonaktifkan, T-08) |
| GET | `/admin/dashboard` | `admin.dashboard` | placeholder |
| GET | `/coach/dashboard` | `coach.dashboard` | placeholder |
| GET | `/mahasiswa/dashboard` | `mahasiswa.dashboard` | placeholder |
| GET/POST | `/coach/projects` | `coach.projects.index/store` | |
| GET | `/coach/projects/create` | `coach.projects.create` | |
| GET | `/coach/projects/{project}` | `coach.projects.show` | |
| POST | `/coach/projects/{project}/members` | `coach.projects.members.store` | |
| DELETE | `/coach/projects/{project}/members/{user}` | `coach.projects.members.destroy` | |

### Terencana (nama final harus konsisten)
| Metode | URI | Nama | Tugas |
|---|---|---|---|
| POST | `/coach/projects/{project}/tasks` | `coach.projects.tasks.store` | T-01 |
| GET/PUT/DELETE | `/coach/tasks/{task}` (`/edit`) | `coach.tasks.edit/update/destroy` | T-01 |
| GET | `/mahasiswa/tasks` | `mahasiswa.tasks.index` | T-02 |
| GET | `/mahasiswa/tasks/{task}` | `mahasiswa.tasks.show` | T-02 |
| PATCH | `/mahasiswa/tasks/{task}/progress` | `mahasiswa.tasks.progress` | T-02 |
| GET | `/mahasiswa/projects/{project}` | `mahasiswa.projects.show` | T-02/T-03 |
| GET/POST | `/coach/projects/{project}/handover` (`/create`) | `coach.handover.index/create/store` | T-03 |
| GET/PUT/DELETE | `/coach/handover/{document}` (`/edit`) | `coach.handover.edit/update/destroy` | T-03 |
| GET | `/handover/{document}/download` | `handover.download` | T-03 (role coach pemilik atau mahasiswa anggota) |
| POST | `/mahasiswa/tasks/{task}/video` | `mahasiswa.videos.store` | T-04 |
| PUT | `/mahasiswa/videos/{video}` | `mahasiswa.videos.update` (resubmit) | T-04 |
| GET | `/coach/videos` | `coach.videos.index` (antrean pending) | T-05 |
| GET | `/coach/videos/{video}` | `coach.videos.show` (player + revisi) | T-05 |
| POST | `/coach/videos/{video}/approve` | `coach.videos.approve` | T-05 |
| POST | `/coach/videos/{video}/revise` | `coach.videos.revise` | T-05 |
| GET | `/admin/users` (+ create, store, edit, update) | `admin.users.*` | T-06 |
| PATCH | `/admin/users/{user}/toggle` | `admin.users.toggle` | T-06 |

## 6. Modul konversi video

`VideoEmbedService::convert(string $url): VideoEmbed` (melempar `InvalidArgumentException`) dan `tryConvert()` (mengembalikan `null`).

| Input | Output `embed_url` |
|---|---|
| `youtube.com/watch?v=ID`, `youtu.be/ID`, `/embed/ID`, `/shorts/ID`, `/live/ID` (ID 11 karakter `[A-Za-z0-9_-]`) | `https://www.youtube-nocookie.com/embed/{ID}` |
| `drive.google.com/file/d/ID/view`, `/preview`, `/file/u/N/d/ID/view`, `open?id=ID`, `uc?id=ID` (ID minimal 10 karakter) | `https://drive.google.com/file/d/{ID}/preview` |

Keamanan: host whitelist, skema hanya http/https, pola ID ketat; URL pengguna tidak pernah disalin mentah ke `src`. `VideoSubmission` memanggil service pada event `saving` bila `video_url` berubah atau `embed_url` kosong. Parameter waktu mulai (`t=`) belum didukung.

## 7. Penyimpanan berkas handover (rancangan T-03)

- Disk privat Laravel (`local`) -> `storage/app/private/handover/{project_id}/`.
- Nama berkas disimpan acak (`->store()`), nama asli di `file_name`, ukuran KB di `file_size`.
- Unduhan lewat `HandoverDownloadController` dengan `Storage::download()` setelah cek: user adalah Coach pemilik proyek atau Mahasiswa anggota proyek.
- Hapus berkas fisik saat dokumen dihapus/diganti.
- Tidak menjalankan symlink `storage:link` untuk berkas ini (tetap privat).

## 8. Konfigurasi `.env` (dev)

```
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simhop_db
DB_USERNAME=root
DB_PASSWORD=
```
Tes otomatis memakai konfigurasi `phpunit.xml` bawaan (SQLite in-memory). Migrasi harus kompatibel dengan SQLite dan MySQL.

## 9. Checklist keamanan

- CSRF pada semua form (`@csrf`), method spoofing (`@method`).
- Mass assignment: `$fillable` eksplisit; `platform`/`embed_url` tidak ada di `$fillable`.
- Output Blade `{{ }}` (escape); jangan `{!! !!}` untuk input pengguna.
- Validasi di server, termasuk: penerima tugas harus anggota proyek; mahasiswa aktif untuk anggota proyek.
- Berkas privat + otorisasi unduhan; validasi mime dan ukuran.
- Rate limiting login (bawaan Breeze).
- Tidak ada rahasia di repo; `.env` di `.gitignore`.

## 10. Kinerja

- Eager loading: mis. `Project::with(['members','tasks.assignee'])`, `VideoSubmission::with(['task.project','user'])`.
- `paginate(10)` untuk daftar; hitung dengan `withCount`.
- Indeks sudah dibuat pada kolom filter utama (lihat DATABASE.md).