# DECISIONS — Log Keputusan Arsitektur dan Produk (ADR ringkas)

Keputusan di bawah **sudah disepakati** (sebagian bersama dosen pembimbing menurut keterangan pemilik proyek). Agent tidak boleh membaliknya. Bila ada alasan kuat untuk berubah, ajukan sebagai keputusan baru (D-xx) kepada pemilik proyek terlebih dahulu.

| ID | Keputusan | Alasan |
|---|---|---|
| D-01 | **7 tabel final**: users, projects, project_user, tasks, handover_documents, video_submissions, revisions. | Cukup untuk dua modul inti; menjaga lingkup D3. |
| D-02 | **Video hibrida**: hanya tautan YouTube Unlisted/Google Drive; sistem menyimpan `video_url` + `embed_url`, tanpa unggah video ke server. | Hemat storage server; sesuai praktik pemagang. |
| D-03 | **Konversi embed di server** (`VideoEmbedService`) dengan whitelist host; kolom `platform`/`embed_url` diisi event `saving`, tidak mass-assignable. | Keamanan iframe; konsistensi data. |
| D-04 | **Tanpa notifikasi** (tidak ada tabel `notifications`); gunakan badge status di dasbor. | Hemat lingkup; tidak menambah tabel. |
| D-05 | **Penugasan mahasiswa ke proyek hanya oleh Coach pemilik** (mengisi `project_user`). Admin tidak ikut. | Admin berfungsi mengontrol akun dan memantau sistem, bukan mengelola proyek (diputuskan pemilik, 3 Nov 2026). |
| D-06 | **Tanpa pendaftaran publik**; semua akun dibuat Admin. | Keamanan akses perusahaan. |
| D-07 | **Kirim ulang video menimpa baris yang sama**: `video_url` diganti, status `pending`, `embed_url` dihitung ulang; riwayat `revisions` tetap utuh. | Linimasa revisi tidak terpecah ke banyak baris. |
| D-08 | **Status tugas `done` otomatis saat video disetujui**; mahasiswa tidak dapat menetapkan `done`. Revisi membuat tugas tetap `in_progress`. | Konsistensi status tugas dan review. |
| D-09 | **Revisi**: catatan wajib; simpan `revisions` + ubah status video dalam satu `DB::transaction`. | Integritas data. |
| D-10 | **Auth**: Laravel Breeze (Blade) + middleware kustom `role`; Tailwind via Vite. | Cepat, dikenal, sesuai D3. |
| D-11 | **Lingkungan lokal Laragon**; Docker hanya konteks studi kasus. | Kesederhanaan. |
| D-12 | **Soft Deletes** diterapkan nanti pada model utama (migrasi tambahan). | Disepakati, ditunda. |
| D-13 | **Relasi FK ke `users` memakai `nullOnDelete`** (created_by, assigned_to, uploaded_by, reviewed_by, coach_id) agar data tidak hilang saat akun dihapus; `video_submissions.user_id` memakai cascade. | Disetujui pemilik; dapat ditinjau dengan soft delete. |
| D-14 | **Metode**: Prototyping/SDLC ringkas (Analisis, Desain, Implementasi, Testing); **Pengujian**: Black Box. | Sesuai rancangan Bab III. |
| D-15 | **Hanya 3 role**: Admin, Coach, Mahasiswa. | Sesuai rancangan. |
| D-16 | **Dokumen akademik jujur**: tidak mengarang sitasi/data/klaim; uji pengguna oleh pemagang diberi label sesuai kenyataan. | Integritas akademik. |
| D-17 | **UI dan pesan dalam Bahasa Indonesia**. | Pengguna Indonesia. |
| D-18 | **Berkas handover privat** + unduhan via controller beserta otorisasi; batas 10 MB per berkas. | Keamanan dan sesuai batasan Bab I. |
| D-19 | **Catatan revisi wajib diisi tanpa batas minimal karakter** (hanya tidak boleh kosong/spasi; batas atas teknis 5000). | Selaras dengan flowchart ("catatan kosong?"); coach dipercaya menulis catatan yang bermakna (diputuskan pemilik, 3 Nov 2026). |
| D-20 | **Kirim ulang video hanya saat status `revision`**; status video selalu ditampilkan agar kondisi saat ini jelas. Ditegakkan di server dan di UI. | Menjaga integritas riwayat dan alur review (diputuskan pemilik, 3 Nov 2026). |
| D-21 | **Peran Admin**: kelola akun dan pantau sistem (dasbor ringkasan); tidak mengakses data proyek. | Pemisahan tugas yang jelas (diputuskan pemilik, 3 Nov 2026). |

## Asumsi yang masih menunggu konfirmasi pemilik

| ID | Asumsi | Dampak bila salah |
|---|---|---|
| A-01 | Satu tugas hanya punya satu pengiriman video aktif (BR-45). | Skema/UI daftar video berubah bila ingin banyak video per tugas. |
| A-02 | Mahasiswa hanya bisa mengirim video bila tugas ditugaskan padanya dan belum `done` (BR-44). | Aturan akses form kirim. |
| A-03 | Daftar tipe berkas handover yang diizinkan (BR-33). | Validasi `mimes`. |
| A-04 | NIM wajib untuk mahasiswa pada form Admin. | Validasi form pengguna. |

## Kronologi singkat konteks

- 28 Okt 2026: fondasi (migrasi, model, auth multi-role, konversi video, manajemen proyek Coach) dibangun dan sebagian diuji pemilik.
- 30 Okt 2026: founder perusahaan menyatakan (lisan) belum ada sistem monitoring/handover dan tidak ada lagi penerimaan pemagang dari kampus. Keputusan pemilik: proyek tetap dilanjutkan sebagai eksplorasi ide TA (penentuan judul resmi dibahas dengan dosen pembimbing di semester 6).