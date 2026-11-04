# DATABASE — Skema Final SIMHOP (7 Tabel)

DBMS: MySQL (Laragon). Database dev: `simhop_db`. Semua tabel memakai `id` bigint unsigned auto-increment dan `timestamps` kecuali dinyatakan lain.

> Migrasi di bawah **sudah dijalankan** di lingkungan pemilik. Jangan diedit. Perubahan skema = migrasi baru.

## 1. Daftar tabel

### 1.1 `users`
Tabel bawaan Laravel/Breeze (migrasi `0001_01_01_000000_create_users_table`) + migrasi `add_role_and_nim_to_users_table`.

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| email_verified_at | timestamp null | tidak dipakai |
| password | string | hashed (cast) |
| **role** | enum(`admin`,`coach`,`mahasiswa`) default `mahasiswa` | ditambahkan |
| **nim** | string(20) null unique | ditambahkan |
| **is_active** | boolean default true | ditambahkan |
| remember_token, timestamps | | bawaan |

### 1.2 `projects`
| Kolom | Tipe | Catatan |
|---|---|---|
| created_by | FK users null, **nullOnDelete** | Coach pembuat |
| name | string | |
| description | text null | |
| status | enum(`active`,`completed`,`archived`) default `active` | |
| start_date, end_date | date null | |

### 1.3 `project_user` (pivot many-to-many users-projects)
| Kolom | Tipe | Catatan |
|---|---|---|
| project_id | FK projects **cascadeOnDelete** | |
| user_id | FK users **cascadeOnDelete** | |
| | **unique(project_id, user_id)** | |

### 1.4 `tasks`
| Kolom | Tipe | Catatan |
|---|---|---|
| project_id | FK projects cascadeOnDelete | |
| created_by | FK users null nullOnDelete | Coach |
| assigned_to | FK users null nullOnDelete | Mahasiswa |
| title | string | |
| description | text null | |
| due_date | date null | |
| status | enum(`todo`,`in_progress`,`done`) default `todo` | |
| progress | unsignedTinyInteger default 0 | 0-100 (%) |
| progress_note | text null | |
| | index(project_id, status) | |

### 1.5 `handover_documents`
| Kolom | Tipe | Catatan |
|---|---|---|
| project_id | FK projects cascadeOnDelete | |
| uploaded_by | FK users null nullOnDelete | |
| title | string | |
| type | enum(`guide`,`tech_stack`,`migration`,`asset`) default `guide` | |
| content | longText null | isi panduan teks |
| file_path | string null | path di disk privat |
| file_name | string null | nama asli berkas |
| file_size | unsignedInteger null | KB; batas 10 MB divalidasi di aplikasi |
| | index(project_id, type) | |

### 1.6 `video_submissions`
| Kolom | Tipe | Catatan |
|---|---|---|
| task_id | FK tasks cascadeOnDelete | proyek diperoleh via `tasks.project_id` |
| user_id | FK users cascadeOnDelete | mahasiswa pengirim |
| title | string | |
| description | text null | |
| platform | enum(`youtube`,`google_drive`) | otomatis dari service |
| video_url | string | tautan asli |
| embed_url | string | otomatis dari service |
| status | enum(`pending`,`approved`,`revision`) default `pending` | |
| reviewed_by | FK users null nullOnDelete | Coach |
| reviewed_at | timestamp null | |
| | index(task_id, status) | |

### 1.7 `revisions`
| Kolom | Tipe | Catatan |
|---|---|---|
| video_submission_id | FK video_submissions cascadeOnDelete | |
| coach_id | FK users null nullOnDelete | |
| note | text | wajib saat revisi |
| | index(video_submission_id) | `created_at` = urutan kronologis |

Catatan: migrasi dibuat ke-7 berkas dengan nama berawalan `2026_10_28_00000X_`. Tabel bawaan Laravel lain (`password_reset_tokens`, `sessions`, `cache`, `jobs`) ikut ada dan bukan bagian dari 7 tabel utama.

## 2. Relasi dan kardinalitas

| Relasi | Kardinalitas | Keterangan |
|---|---|---|
| users — projects | **M:N** via `project_user` | anggota proyek |
| users — projects (`created_by`) | 1:N (opsional) | pembuat proyek |
| projects — tasks | 1:N | |
| users — tasks (`created_by`) | 1:N (opsional) | |
| users — tasks (`assigned_to`) | 1:N (opsional) | |
| projects — handover_documents | 1:N | |
| users — handover_documents (`uploaded_by`) | 1:N (opsional) | |
| tasks — video_submissions | 1:N (praktiknya 1 aktif per tugas, BR-45) | |
| users — video_submissions (`user_id`) | 1:N | |
| users — video_submissions (`reviewed_by`) | 1:N (opsional) | |
| video_submissions — revisions | 1:N | |
| users — revisions (`coach_id`) | 1:N (opsional) | |

## 3. Peta relasi Eloquent (sudah ada di model)

- `User`: `projects()` belongsToMany; `createdProjects()`, `createdTasks()`, `assignedTasks()`, `handoverDocuments()` (uploaded_by), `videoSubmissions()`, `reviewedVideos()` (reviewed_by), `revisions()` (coach_id) hasMany.
- `Project`: `creator()` belongsTo; `members()`, `students()` (filter role mahasiswa) belongsToMany; `tasks()`, `handoverDocuments()` hasMany.
- `Task`: `project()`, `creator()`, `assignee()` belongsTo; `videoSubmissions()` hasMany; `markAsDone()`.
- `HandoverDocument`: `project()`, `uploader()` belongsTo; `MAX_FILE_KB = 10240`.
- `VideoSubmission`: `task()`, `user()`, `reviewer()` belongsTo; `revisions()` hasMany (urut `oldest()`); event `saving` mengisi `platform` + `embed_url`.
- `Revision`: `videoSubmission()`, `coach()` belongsTo.

## 4. Perubahan terencana (belum dibuat)

- **Soft Deletes** (`deleted_at`) pada model utama (kandidat: `projects`, `tasks`, `video_submissions`) lewat migrasi baru. Kesepakatan: diterapkan nanti; jangan dikerjakan sebelum diminta. Saat diterapkan, tinjau ulang perilaku cascade.
- Tidak ada rencana tabel baru. Tabel `notifications` **dilarang** (lihat DECISIONS D-04).

## 5. Seeder dev (`DatabaseSeeder`)

Membuat 4 akun: `admin@example.com`, `coach@example.com` (Coach Uji), `coach2@example.com` (Coach Dua), `mahasiswa@example.com` (Mahasiswa Uji, NIM 2024001); password `password123`. Dapat diperluas dengan data demo (proyek, tugas, dokumen) pada tugas T-10.