# BUSINESS RULES — Aturan Bisnis SIMHOP

Aturan di sini sudah disepakati pemilik proyek. Agent **tidak boleh mengubahnya** tanpa persetujuan. Bagian bertanda **ASUMSI** belum final dan harus dikonfirmasi sebelum diimplementasikan.

## 1. Akun dan akses

- BR-01. Tidak ada pendaftaran publik. Semua akun dibuat Admin. Rute `register` Breeze dihapus (`/register` = 404).
- BR-02. Role hanya `admin`, `coach`, `mahasiswa` (kolom `users.role`). Default role `mahasiswa`.
- BR-03. Akun `is_active = false` tidak boleh memakai sistem. Middleware `role` mengeluarkannya (logout + invalidate session) dengan pesan "Akun Anda dinonaktifkan. Hubungi Admin."
- BR-04. Setelah login, `/dashboard` mengalihkan ke `{role}.dashboard`.
- BR-05. Akses lintas role ke halaman role lain menghasilkan HTTP 403. Tamu dialihkan ke `login`.
- BR-06. Admin tidak boleh: menonaktifkan akunnya sendiri, mengubah role akunnya sendiri, atau menghapus akunnya sendiri. Harus tersisa minimal satu Admin aktif.
- BR-07. Pengguna tidak boleh menghapus akunnya sendiri lewat halaman Profil Breeze (fitur itu dinonaktifkan; akun dikelola Admin).
- BR-08. Verifikasi email tidak dipakai (akun dibuat Admin).
- BR-09. Peran Admin terbatas pada **mengelola akun pengguna dan memantau sistem** (dasbor ringkasan: jumlah pengguna per role, akun nonaktif). Admin tidak membuat/mengubah proyek, tugas, handover, atau video.

## 2. Kepemilikan dan hak akses data

| Sumber daya | Admin | Coach | Mahasiswa |
|---|---|---|---|
| Pengguna (CRUD) | Penuh | Tidak | Tidak |
| Proyek | Tidak | CRUD **hanya miliknya** (`created_by`) | Hanya lihat proyek yang diikuti |
| Anggota proyek (`project_user`) | Tidak | Tambah/keluarkan di proyek miliknya | Tidak |
| Tugas | Tidak | CRUD pada proyek miliknya | Lihat tugas yang ditugaskan kepadanya; ubah status+progres+catatan |
| Dokumen handover | Tidak | CRUD pada proyek miliknya | Lihat dan unduh pada proyek yang diikuti |
| Video | Tidak | Lihat dan review video pada tugas di proyek miliknya | Kirim/kirim ulang untuk tugas miliknya; lihat video dan revisinya sendiri |
| Revisi | Tidak | Buat pada video di proyek miliknya | Baca pada video miliknya |

2.1 Keputusan penugasan (final, D-05): **hanya Coach pemilik** proyek yang menugaskan mahasiswa ke proyek (mengisi `project_user`). Admin tidak menyentuh data proyek: perannya mengelola akun dan memantau sistem lewat dasbor ringkasan (BR-09).

Prinsip: pemeriksaan kepemilikan dilakukan **di server** (FormRequest `authorize()` atau `abort_unless(..., 403)`), tidak cukup hanya menyembunyikan tombol di UI.

## 3. Proyek dan anggota

- BR-10. `projects.created_by` = Coach pembuat. Hanya pemilik yang mengelola proyek.
- BR-11. Status proyek: `active`, `completed`, `archived` (default `active`).
- BR-12. Anggota proyek yang bisa ditambahkan: pengguna `role = mahasiswa` dan `is_active = true`. Tidak boleh duplikat (unique `project_id,user_id`).
- BR-13. Mengeluarkan anggota dari proyek tidak menghapus tugas/video yang sudah ada.

## 4. Tugas (tasks)

- BR-20. Tugas dibuat oleh Coach pemilik proyek (`created_by`), milik satu proyek (`project_id`).
- BR-21. `assigned_to` boleh kosong ("belum ditugaskan"); bila diisi harus mahasiswa **anggota proyek yang sama**.
- BR-22. Status tugas: `todo` (default), `in_progress`, `done`. `progress` 0-100.
- BR-23. Coach **tidak** mengubah status/progres lewat form tugas (hanya judul, deskripsi, tenggat, penerima).
- BR-24. Mahasiswa **hanya** boleh mengubah status antara `todo` dan `in_progress`, serta `progress` dan `progress_note`, pada tugas yang ditugaskan kepadanya. Mahasiswa **tidak boleh** menetapkan `done`.
- BR-25. Status `done` (dan `progress = 100`) hanya terjadi otomatis saat video terkait disetujui (`Task::markAsDone()`).
- BR-26. Menghapus tugas ikut menghapus video dan revisinya (cascade). UI wajib konfirmasi.

### State machine tugas
```
todo --(mahasiswa mulai)--> in_progress --(video approved)--> done
 ^                                |
 |                                +--(video revision)--> tetap in_progress
 +--(mahasiswa batal mulai)-------+
```
Tugas `done` tidak dapat diubah mahasiswa lagi.

## 5. Handover

- BR-30. Dokumen handover milik satu proyek; `type` salah satu: `guide`, `tech_stack`, `migration`, `asset`.
- BR-31. Berkas aset opsional; ukuran maksimal **10 MB (10240 KB)** per berkas (`HandoverDocument::MAX_FILE_KB`). Validasi `max:10240`. `file_size` disimpan dalam KB.
- BR-32. Berkas disimpan di disk **privat** (`storage/app/private/handover/{project_id}/...`) dan diunduh lewat controller dengan otorisasi (Coach pemilik atau Mahasiswa anggota proyek). Tidak memakai tautan publik langsung.
- BR-33. Tipe berkas yang diizinkan (ASUMSI, konfirmasi): pdf, zip, txt, md, doc/docx, png, jpg. Eksekusi/skrip (`.php`, `.exe`, `.sh`, `.bat`) ditolak.
- BR-34. Tujuan modul: pengganti pengunduhan image OS besar. Berkas besar tidak boleh diunggah.

## 6. Video dan review

- BR-40. Sumber video hanya YouTube (disarankan *Unlisted*) atau Google Drive. Sistem hanya menyimpan `video_url` dan `embed_url`; tidak ada unggahan video.
- BR-41. `video_url` divalidasi dengan `ValidVideoUrl`; `platform` dan `embed_url` diisi otomatis (event `saving` pada `VideoSubmission`). Kolom itu **tidak** mass-assignable.
- BR-42. Video untuk Drive hanya bisa diputar bila akses berkas "Siapa saja yang memiliki link"; tampilkan petunjuk pada form.
- BR-43. Status video: `pending` (default), `approved`, `revision`.
- BR-44. Mahasiswa hanya boleh mengirim video untuk **tugas yang ditugaskan kepadanya**, ketika tugas tidak `done`.
- BR-45. **ASUMSI (konfirmasi)**: satu tugas memiliki satu pengiriman video aktif. Bila sudah ada, pengiriman berikutnya adalah **kirim ulang** (BR-47), bukan baris baru.
- BR-46. **Setujui**: dalam `DB::transaction` — `status = approved`, `reviewed_by = coach`, `reviewed_at = now()`, lalu `task->markAsDone()`.
- BR-47. **Kirim ulang (resubmit)**: hanya saat status `revision`. Menimpa baris `video_submissions` yang sama: `video_url` diganti, `embed_url`/`platform` dihitung ulang otomatis, `status = pending`, `reviewed_by` dan `reviewed_at` dikosongkan. Riwayat `revisions` tetap utuh.
- BR-48. **Minta revisi**: `note` **wajib diisi** (tidak boleh kosong atau hanya berisi spasi). **Tidak ada batas minimal jumlah karakter.** Hanya ada batas atas teknis 5000 karakter agar data tidak berlebihan. Dalam `DB::transaction` — buat `revisions` (`coach_id`, `note`), `status = revision`, `reviewed_by`, `reviewed_at`. Tugas tetap `in_progress`.
- BR-49. Video berstatus `pending` atau `approved` tidak dapat diubah mahasiswa. Hanya `revision` yang boleh dikirim ulang. Aturan ini ditegakkan **di server** (FormRequest `authorize()`), dan di UI tombol/form "Kirim Ulang" hanya muncul saat status `revision`; pada status lain UI hanya menampilkan badge status.
- BR-50. Coach hanya me-review video pada tugas di **proyek miliknya**.
- BR-51. Riwayat revisi ditampilkan kronologis (terlama ke terbaru, `created_at`), dengan nama coach dan tanggal.
- BR-52. Tidak ada notifikasi. Mahasiswa melihat status lewat **badge** di dasbor/daftar tugas: pending (kuning), approved (hijau), revision (oranye).

### State machine video
```
(belum ada) --kirim--> pending --setujui--> approved (final, tugas done)
                         |
                         +--minta revisi (catatan wajib)--> revision --kirim ulang--> pending
```

## 7. Validasi ringkas

| Field | Aturan |
|---|---|
| `projects.name` | required, string, max 255 |
| `projects.end_date` | nullable, date, after_or_equal start_date |
| `tasks.title` | required, string, max 255 |
| `tasks.assigned_to` | nullable, anggota proyek, role mahasiswa |
| `tasks.progress` | integer 0-100 |
| `video_url` | required, string, `ValidVideoUrl` |
| `revisions.note` | required, string, max 5000 (tanpa minimal) |
| berkas handover | file, max:10240, mimes sesuai BR-33 |
| `users.email` | required, email, unique |
| `users.nim` | nullable, unique, wajib untuk role mahasiswa (ASUMSI) |