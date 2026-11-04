# PRD — Product Requirements Document SIMHOP

Versi: 1.0 (28 Oktober 2026). Pemilik: penulis TA (mahasiswa D3 Teknik Informatika, semester 5, Politeknik Negeri Cilacap).

## 1. Latar belakang dan masalah

Studi kasus: program magang divisi pemrograman di PT Teknologi Server Indonesia (merek: Xcode), Yogyakarta, perusahaan keamanan siber dan pengembangan TI. Observasi penulis sejak Agustus 2026:

**Masalah 1 — Handover proyek tidak efisien.** Saat melanjutkan proyek lama, serah terima dilakukan dengan mengunduh image sistem operasi Ubuntu versi lama berukuran sangat besar (memakan data internet, waktu, ruang penyimpanan). Dokumentasi teknis (arsitektur, tech stack, skema database, panduan migrasi) tidak tersedia secara terstruktur sehingga pemagang baru sulit menyiapkan development environment sendiri.

**Masalah 2 — Monitoring dan review video buruk.** Pemagang wajib merekam video dokumentasi untuk setiap proyek/fitur yang selesai, mengunggahnya ke Google Drive pribadi, lalu mengirim tautan lewat WhatsApp ke coach. Akibatnya: (a) tautan tenggelam di antara chat, (b) coach sangat sibuk dan sering tidak di kantor sehingga respons tertunda berhari-hari, (c) catatan revisi berupa chat teks yang tidak terikat pada video sehingga riwayat revisi dan progres sulit dilacak.

## 2. Tujuan produk

1. Menyediakan **direktori dokumentasi handover terpusat** (panduan, spesifikasi tech stack, catatan migrasi, berkas aset ringan) agar pemagang baru tidak perlu mengunduh image OS besar.
2. Menyediakan **alur kirim, review, dan revisi video** yang terpusat: mahasiswa memasukkan tautan video, coach memutarnya langsung di web dan memberi status *Approved* atau *Revisi* dengan catatan yang terikat pada video tersebut.
3. Menyediakan **kontrol akses multi-role** (Admin, Coach, Mahasiswa) tanpa pendaftaran publik.

### Kriteria keberhasilan (untuk Bab IV)
- Seluruh alur utama (lihat bagian 6) lulus *black box testing*.
- Seluruh pengguna uji dapat menyelesaikan skenario inti tanpa bantuan (diukur lewat lembar skenario dan kuesioner; data aktual **diisi oleh penulis**, jangan dikarang).

## 3. Bukan tujuan (Out of Scope)

- Pendaftaran publik/self-register.
- Chat/percakapan langsung, notifikasi (email/push/tabel notifikasi). Status ditampilkan sebagai badge di dasbor.
- Penyimpanan atau streaming berkas video di server. Hanya tautan YouTube (Unlisted) atau Google Drive.
- Penilaian akhir/nilai akademik magang untuk kampus.
- Aplikasi mobile native, API publik, multi-bahasa, multi-perusahaan (multi-tenant).
- Docker sebagai lingkungan sistem (Docker hanya konteks proyek studi kasus).

## 4. Pengguna dan peran

| Peran | Siapa | Tujuan utama |
|---|---|---|
| **Admin** | Pengelola sistem | Mengelola akun (buat, ubah, aktif/nonaktif) dan memantau sistem lewat dasbor ringkasan; tidak mengelola proyek |
| **Coach** | Pembimbing industri (mentor) yang sibuk | Membuat proyek, tugas, dokumen handover; menugaskan mahasiswa; me-review video dengan cepat |
| **Mahasiswa** | Peserta magang | Membaca handover, mengerjakan tugas, memperbarui progres, mengirim tautan video, melihat status dan riwayat revisi |

## 5. Cerita pengguna (User Stories)

**Autentikasi dan akun**
- US-A1: Sebagai pengguna, saya login dengan email dan password, lalu langsung diarahkan ke dasbor sesuai role saya.
- US-A2: Sebagai Admin, saya membuat, mengubah, dan menonaktifkan akun Admin/Coach/Mahasiswa, serta menentukan role.
- US-A3: Sebagai Admin, saya mengaktifkan atau menonaktifkan akun; akun nonaktif tidak bisa memakai sistem.
- US-A4: Sebagai pengguna, saya melihat dasbor ringkasan sesuai role saya dan mengubah nama, email, dan kata sandi lewat halaman Profil.

**Proyek dan penugasan**
- US-C1: Sebagai Coach, saya membuat proyek (nama, deskripsi, tanggal) yang menjadi milik saya.
- US-C2: Sebagai Coach, saya menambahkan atau mengeluarkan mahasiswa dari proyek saya (tabel pivot `project_user`).
- US-C3: Sebagai Coach, saya membuat tugas pada proyek saya dan menugaskannya ke anggota proyek.

**Handover**
- US-H1: Sebagai Coach, saya mengisi dokumen handover proyek (panduan, tech stack, catatan migrasi, aset pendukung maks. 10 MB per berkas).
- US-H2: Sebagai Mahasiswa, saya membaca dokumen handover proyek yang saya ikuti dan mengunduh asetnya, sehingga tidak perlu mengunduh image OS besar.

**Tugas dan progres**
- US-M1: Sebagai Mahasiswa, saya melihat proyek yang saya ikuti dan tugas yang ditugaskan kepada saya.
- US-M2: Sebagai Mahasiswa, saya mengubah status tugas (todo ke in_progress), mengisi persentase progres, dan catatan progres.

**Video dan review**
- US-V1: Sebagai Mahasiswa, saya memasukkan tautan video (YouTube/Drive) untuk tugas saya; sistem memvalidasi dan mengonversinya menjadi embed.
- US-V2: Sebagai Coach, saya melihat antrean video berstatus *pending*, memutar video di halaman web tanpa pindah tab.
- US-V3: Sebagai Coach, saya menyetujui video (tugas otomatis selesai) atau meminta revisi dengan catatan wajib diisi.
- US-V4: Sebagai Mahasiswa, saya melihat badge status video dan riwayat catatan revisi secara kronologis, lalu mengirim ulang tautan perbaikan pada baris video yang sama.

## 6. Alur utama (ringkas; flowchart lengkap ada di FigJam penulis)

1. **Login dan routing role**: Login, middleware cek role dan `is_active`, dasbor `/admin|coach|mahasiswa/dashboard`.
2. **Proyek dan handover**: Coach buat proyek, tugaskan mahasiswa, isi dokumen handover (validasi 10 MB), Mahasiswa baca dan unduh.
3. **Tugas dan progres**: Coach buat tugas, Mahasiswa `todo`, `in_progress`, perbarui progres; tugas menjadi `done` otomatis saat video disetujui.
4. **Video dan review**: Mahasiswa kirim tautan, sistem validasi (`ValidVideoUrl`), konversi (`VideoEmbedService`), simpan `pending`. Coach putar, lalu Setujui, atau Minta Revisi (catatan wajib; `DB::transaction`: simpan revisi + status `revision`). Mahasiswa mengirim ulang (baris sama ditimpa, status kembali `pending`).

## 7. Kebutuhan fungsional (FR)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-01 | Login/logout (Laravel Breeze), tanpa register publik | Wajib |
| FR-02 | Middleware multi-role (`role:admin,coach,mahasiswa`) dan pengalihan dasbor per role | Wajib |
| FR-03 | Akun nonaktif otomatis dikeluarkan | Wajib |
| FR-04 | CRUD pengguna oleh Admin (role, NIM, aktif/nonaktif) | Wajib |
| FR-05 | CRUD proyek oleh Coach pemilik, kelola anggota via `project_user` | Wajib |
| FR-06 | CRUD tugas oleh Coach pemilik proyek; penerima harus anggota proyek | Wajib |
| FR-07 | Mahasiswa melihat tugasnya, memperbarui status (todo/in_progress), progres, catatan | Wajib |
| FR-08 | CRUD dokumen handover oleh Coach; unggah berkas maks. 10 MB; unduh oleh anggota proyek | Wajib |
| FR-09 | Mahasiswa mengirim `video_url`, validasi `ValidVideoUrl`, `embed_url` otomatis | Wajib |
| FR-10 | Coach memutar video lewat `<x-video-player>` | Wajib |
| FR-11 | Coach menyetujui video, lalu `status=approved`, tugas `done` (progress 100) | Wajib |
| FR-12 | Coach meminta revisi dengan catatan wajib, dalam `DB::transaction` | Wajib |
| FR-13 | Linimasa riwayat revisi terurut kronologis pada tiap video | Wajib |
| FR-14 | Kirim ulang video menimpa baris yang sama, status kembali `pending` | Wajib |
| FR-15 | Dasbor per role berisi ringkasan (jumlah tugas, antrean review, dsb.) | Sebaiknya |
| FR-16 | Badge status (pending/approved/revision) di dasbor mahasiswa | Wajib |
| FR-17 | Soft Deletes pada model utama | Nanti |
| FR-18 | Halaman Profil: ubah nama/email/kata sandi (tanpa hapus akun sendiri) | Wajib |

## 8. Kebutuhan non-fungsional

- **Keamanan**: password di-hash, CSRF aktif, otorisasi kepemilikan di server, validasi ketat, host whitelist untuk embed, berkas aset disimpan di disk privat dan diunduh lewat controller beserta otorisasi.
- **Kegunaan**: UI Bahasa Indonesia, responsif (Tailwind), konfirmasi sebelum hapus, pesan error jelas.
- **Kinerja**: eager loading, paginasi daftar (10-15 baris).
- **Kemudahan perawatan**: Laravel 12 idiomatik, MVC, controller tipis, logika bisnis di model/service.
- **Portabilitas**: tidak terikat satu perusahaan; data perusahaan hanya berupa isi data.

## 9. Konteks akademik dan batas klaim (penting bagi agent)

- Proposal TA D3; Bab I sudah final, Bab III (perancangan) disusun, Bab IV berisi pengujian.
- Pernyataan founder (didengar langsung oleh penulis, 30 Oktober 2026, belum dikonfirmasi tertulis): perusahaan belum punya sistem monitoring pemagang (pengawasan oleh mentor); sistem handover ingin dibuat namun belum jadi; perusahaan tidak akan menerima pemagang dari kampus untuk periode berikutnya. **Jangan** menulis klaim di luar itu (mis. "restrukturisasi", "tidak punya kapasitas") dan **jangan** menyatakan sistem "terbukti berhasil" sebelum pengujian nyata.
- Pengujian pengguna dilakukan oleh peserta magang yang memerankan Coach/Mahasiswa. Dokumentasikan secara jujur siapa penguji dan perannya; jangan menyatakan coach/pimpinan perusahaan mencoba bila tidak benar.
- Dokumen akademik: gunakan data dari penulis; tandai `[ISI OLEH PENULIS]` bila belum ada.

## 10. Glosarium

- **Handover**: serah terima proyek dari pengembang/pemagang lama ke baru.
- **Coach**: pembimbing industri (mentor) di perusahaan.
- **Embed URL**: tautan khusus yang aman dirender di `<iframe>` (hasil konversi `video_url`).
- **Resubmit**: mengirim ulang tautan video setelah status *revision*; menimpa baris yang sama.
- **Pivot**: tabel perantara many-to-many (`project_user`).