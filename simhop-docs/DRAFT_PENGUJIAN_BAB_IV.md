# DRAFT PENGUJIAN DAN EVALUASI SISTEM (BAB IV)
## Sistem Informasi Monitoring dan Handover Proyek Magang (SIMHOP)
**Program Studi D3 Teknik Informatika — Politeknik Negeri Cilacap**

> **Petunjuk bagi Penulis:**
> Dokumen ini disusun untuk membantu draf laporan Tugas Akhir Bab IV (Hasil dan Pembahasan) serta Bab III (Metodologi/Perancangan Pengujian).
> Sesuai prinsip kejujuran akademik (*DECISIONS D-16*), kolom **Hasil Pengujian Manual** dan **Kuesioner Pengguna** ditandai dengan `[ISI OLEH PENULIS]` agar diisi berdasarkan pengujian riil yang dilakukan penulis bersama rekan penguji di lapangan.

---

## 1. Lingkungan Pengujian

- **Perangkat Keras (Pengembang & Server Lokal)**: Laptop Axioo, Processor Intel/AMD, RAM 8+ GB, SSD.
- **Perangkat Lunak Server**: Laragon (PHP 8.4, MySQL 8.0, Apache/Nginx).
- **Framework & Frontend**: Laravel 12.x, Laravel Breeze, Tailwind CSS via Vite.
- **Basis Data**: MySQL (`simhop_db`) untuk lingkungan riil/demo, SQLite in-memory untuk automated testing.
- **Akun Uji Tersedia**:
  - Admin: `admin@example.com` / `password123`
  - Coach 1: `coach@example.com` (Coach Uji) / `password123`
  - Coach 2: `coach2@example.com` (Coach Dua) / `password123`
  - Mahasiswa 1: `mahasiswa@example.com` (NIM: 2024001) / `password123`

---

## 2. Pengujian Fungsional (Black Box Testing)

Pengujian fungsional dilakukan dengan metode *Black Box Testing* untuk memverifikasi apakah keluaran sistem telah sesuai dengan spesifikasi kebutuhan yang ditetapkan tanpa melihat struktur internal kode.

### 2.1 Pengujian Modul Autentikasi & Akses Peran

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-A01** | Login pengguna valid untuk tiap role | Email & password benar (Admin, Coach, Mahasiswa) | Sistem berhasil mengautentikasi dan mengalihkan pengguna ke dasbor spesifik perannya (`/admin/dashboard`, `/coach/dashboard`, atau `/mahasiswa/dashboard`). | **Lulus** (`AuthenticationTest`, `RoleAccessTest`) | [ISI OLEH PENULIS: Sesuai / Tidak] | [ISI: Berhasil] |
| **BB-A02** | Login dengan kata sandi salah | Email terdaftar, kata sandi keliru (`wrong-password`) | Sistem menolak autentikasi, menampilkan pesan validasi error, dan pengguna tetap berada di form login. | **Lulus** (`AuthenticationTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A03** | Percobaan login oleh akun yang dinonaktifkan (`is_active = false`) | Kredensial akun nonaktif yang benar | Sistem langsung menolak masuk dan menampilkan pesan: *"Akun Anda dinonaktifkan. Hubungi Admin."* | **Lulus** (`AuthenticationTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A04** | Pengguna yang dinonaktifkan saat sesi masih aktif | Akun sedang login, lalu Admin menonaktifkan akun tersebut | Pada permintaan request berikutnya, middleware langsung me-logout sesi pengguna dan mengalihkan ke `/login` dengan notifikasi nonaktif. | **Lulus** (`RoleAccessTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A05** | Akses ke rute registrasi publik (`/register`) | Mengakses URL `http://127.0.0.1:8000/register` | Sistem mengembalikan HTTP response **404 Not Found** (pendaftaran publik ditiadakan sesuai batasan sistem internal). | **Lulus** (`RoleAccessTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A06** | Akses halaman role lain (akses tidak sah) | Mahasiswa membuka URL `/admin/dashboard` atau `/coach/dashboard` | Sistem menolak akses dengan respons HTTP **403 Forbidden** dan menampilkan halaman error kustom berbahasa Indonesia. | **Lulus** (`RoleAccessTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A07** | Pengguna tamu (*guest*) membuka halaman internal | Membuka URL dasbor tanpa login | Sistem secara otomatis mengalihkan pengguna ke halaman login (`/login`). | **Lulus** (`RoleAccessTest`, `ExampleTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A08** | Logout dari sistem | Menekan tombol "Keluar" | Sesi pengguna dihapus dari server dan pengguna dialihkan kembali ke halaman utama/login. | **Lulus** (`AuthenticationTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-A09** | Percobaan hapus akun mandiri lewat profil | Mengirim request `DELETE /profile` | Sistem menolak aksi dengan HTTP **403 Forbidden** (akun hanya dikelola terpusat oleh Administrator). | **Lulus** (`ProfileTest`) | [ISI OLEH PENULIS] | [ISI] |

---

### 2.2 Pengujian Modul Manajemen Pengguna (Sisi Admin)

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-U01** | Admin membuat akun baru (Admin/Coach/Mahasiswa) | Nama, email unik, role, NIM (wajib untuk mahasiswa), password awal min. 8 karakter | Akun baru berhasil tersimpan di database dan dapat digunakan untuk login ke sistem. | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U02** | Pembuatan akun dengan email yang sudah terdaftar | Memasukkan email yang sudah ada di sistem | Sistem menolak data dan menampilkan pesan validasi bahwa email telah digunakan. | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U03** | Pembuatan akun Mahasiswa tanpa mengisi NIM | Form mahasiswa diisi lengkap tanpa NIM | Sistem memvalidasi bahwa NIM wajib diisi untuk role mahasiswa. | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U04** | Admin memperbarui data pengguna & reset password | Mengubah nama, email, role, atau mengisi kolom password baru | Data pengguna terbarukan dan kata sandi baru dapat digunakan untuk login. | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U05** | Admin mengaktifkan / menonaktifkan status akun lain | Klik tombol nonaktifkan pada akun coach/mahasiswa | Status akun beralih antara aktif dan nonaktif; akun nonaktif tidak dapat lagi login. | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U06** | Admin menonaktifkan akun miliknya sendiri | Klik toggle nonaktif pada akun sendiri | Sistem menolak dengan HTTP **403 Forbidden** (Admin dilarang menonaktifkan diri sendiri). | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U07** | Admin mengubah role akunnya sendiri | Mengedit akun sendiri dan mengganti role ke Mahasiswa/Coach | Sistem menolak dengan HTTP **403 Forbidden** (Admin tidak boleh mencopot hak akses sendiri). | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-U08** | Admin menonaktifkan admin aktif terakhir | Mencoba menonaktifkan admin satu-satunya yang masih aktif | Sistem menolak aksi dengan HTTP **403 Forbidden** (minimal harus tersisa satu admin aktif). | **Lulus** (`AdminUserTest`) | [ISI OLEH PENULIS] | [ISI] |

---

### 2.3 Pengujian Modul Proyek & Tim Mahasiswa (Sisi Coach)

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-P01** | Coach membuat proyek baru | Nama proyek, deskripsi, tanggal mulai, tanggal selesai | Proyek tersimpan dengan kepemilikan Coach yang sedang login, dialihkan ke halaman detail proyek. | **Lulus** (`CoachProjectTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-P02** | Validasi tanggal selesai lebih awal dari tanggal mulai | Tanggal selesai: `2026-08-01`, tanggal mulai: `2026-09-01` | Form ditolak dengan pesan validasi bahwa tanggal selesai harus sesudah atau sama dengan tanggal mulai. | **Lulus** (`CoachProjectTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-P03** | Coach menambahkan mahasiswa ke tim proyek | Memilih mahasiswa dari dropdown anggota tersedia | Mahasiswa terdaftar sebagai anggota di tabel pivot `project_user` dan tampil di daftar tim proyek. | **Lulus** (`CoachProjectTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-P04** | Coach mengeluarkan mahasiswa dari tim proyek | Klik tombol keluarkan anggota tim | Mahasiswa terhapus dari anggota tim; data tugas/video yang telah dibuat tetap terjaga aman. | **Lulus** (`CoachProjectTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-P05** | Coach membuka atau mengedit proyek milik Coach lain | Mengakses URL proyek milik coach lain via manipulasi ID URL | Akses diblokir oleh server dengan respons HTTP **403 Forbidden**. | **Lulus** (`CoachProjectTest`) | [ISI OLEH PENULIS] | [ISI] |

---

### 2.4 Pengujian Modul Tugas / Tasks (Sisi Coach & Mahasiswa)

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-T01** | Coach membuat tugas untuk mahasiswa anggota proyek | Judul tugas, deskripsi, tenggat waktu, penerima tugas (anggota proyek) | Tugas berhasil dibuat dengan status awal `todo` dan progres 0%. | **Lulus** (`CoachTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T02** | Coach menetapkan tugas kepada mahasiswa non-anggota | Memasukkan ID mahasiswa yang tidak terdaftar di proyek tersebut | Server memvalidasi dan menolak penugasan (penerima wajib anggota proyek). | **Lulus** (`CoachTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T03** | Coach mengubah dan menghapus tugas | Memperbarui rincian tugas atau menghapus tugas | Data terbarui; penghapusan tugas cascade membersihkan entri video/revisi terkait. | **Lulus** (`CoachTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T04** | Mahasiswa melihat daftar tugas miliknya | Membuka dasbor atau halaman tugas mahasiswa | Mahasiswa hanya melihat daftar tugas yang secara eksplisit ditugaskan kepada dirinya. | **Lulus** (`MahasiswaTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T05** | Mahasiswa memperbarui progres tugas (`todo` -> `in_progress`) | Mengubah status menjadi `in_progress`, persentase 60%, dan catatan progres | Status dan progres terbarui dan tampil pada dasbor Coach dan Mahasiswa. | **Lulus** (`MahasiswaTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T06** | Mahasiswa mencoba menandai tugas menjadi `done` secara sepihak | Mengirim parameter status `done` dari form atau request | Ditolak oleh validasi server (status `done` hanya terjadi otomatis saat video disetujui Coach). | **Lulus** (`MahasiswaTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T07** | Mahasiswa memasukkan persentase progres di luar rentang 0-100 | Memasukkan progres `-10` atau `150` | Server menolak input dengan pesan validasi nilai harus di antara 0 dan 100. | **Lulus** (`MahasiswaTaskTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-T08** | Mahasiswa mencoba memperbarui tugas milik mahasiswa lain | Mengakses URL tugas milik rekan lain via ID | Server menolak akses dengan HTTP **403 Forbidden**. | **Lulus** (`MahasiswaTaskTest`) | [ISI OLEH PENULIS] | [ISI] |

---

### 2.5 Pengujian Modul Handover Proyek

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-H01** | Coach membuat dokumen handover berbasis teks | Judul, tipe (`tech_stack`/`guide`/`migration`), isi teks teknis | Dokumen tersimpan dan langsung dapat dibaca oleh mahasiswa anggota proyek. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-H02** | Coach mengunggah berkas aset handover valid (<= 10 MB) | File ZIP/PDF ukuran 5 MB | Berkas tersimpan di penyimpanan privat server, metadata (nama & ukuran) tercatat di database. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-H03** | Coach mengunggah berkas melebihi batas ukuran (> 10 MB) | Berkas file berukuran 12 MB | Unggahan ditolak dengan pesan error batasan maksimal ukuran berkas 10 MB. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-H04** | Coach mengunggah ekstensi berkas yang dilarang (.exe / .php) | Berkas berekstensi berbahaya | Sistem menolak unggahan berdasarkan whitelist format dokumen/arsip aman. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-H05** | Mahasiswa anggota tim mengunduh berkas handover | Klik link unduh dokumen pada proyeknya | Berkas berhasil diunduh via stream controller dengan nama berkas asli. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-H06** | Mahasiswa non-anggota mencoba mengunduh berkas handover | Mengakses direct URL unduhan dokumen proyek lain | Server memblokir pengunduhan dengan HTTP **403 Forbidden**. | **Lulus** (`HandoverTest`) | [ISI OLEH PENULIS] | [ISI] |

---

### 2.6 Pengujian Modul Monitoring & Video Review (Inti Sistem)

| ID | Skenario Uji | Data Masukan / Langkah | Hasil yang Diharapkan | Hasil Tes Otomatis (PHPUnit) | Hasil Pengujian Manual | Status |
|:---|:---|:---|:---|:---|:---|:---|
| **BB-V01** | Mahasiswa mengirim tautan video YouTube valid | Tautan watch, shortlink youtu.be, shorts, atau embed | Sistem mengekstrak ID video, mengonversi `embed_url`, status menjadi `pending`. | **Lulus** (`VideoSubmissionTest`, `VideoEmbedServiceTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V02** | Mahasiswa mengirim tautan Google Drive valid | Tautan sharing file drive (`/file/d/{id}/view`) | Sistem mengonversi menjadi URL `/preview`, status tersimpan `pending`. | **Lulus** (`VideoSubmissionTest`, `VideoEmbedServiceTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V03** | Mahasiswa mengirim tautan bukan YouTube/Drive (misal Vimeo, TikTok, phising) | URL platform di luar whitelist | Input ditolak oleh aturan validasi `ValidVideoUrl` dengan pesan error bahasa Indonesia. | **Lulus** (`VideoSubmissionTest`, `ValidVideoUrlRuleTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V04** | Coach membuka halaman review video | Membuka video dari antrean antarmuka review | Video diputar langsung di web melalui pemutar aman (`<iframe>` dari domain whitelist). | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V05** | Coach menyetujui video dokumentasi (*Approve*) | Klik tombol hijau "Setujui Video Ini" | Dalam satu transaksi atomik: status video menjadi `approved`, nama & waktu coach tercatat, dan **tugas otomatis berubah menjadi `done` (progress 100%)**. | **Lulus** (`VideoReviewTest`, `TaskModelTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V06** | Coach meminta revisi tanpa mengisi catatan revisi | Klik "Kirim Catatan Revisi" dengan catatan kosong/spasi | Form ditolak dengan validasi pesan bahwa catatan revisi wajib diisi. | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V07** | Coach meminta revisi dengan catatan yang jelas | Mengisi catatan koreksi dan mengirim form revisi | Dalam transaksi atomik: status video beralih ke `revision`, baris revisi tercatat di tabel `revisions`, dan status tugas tetap `in_progress`. | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V08** | Mahasiswa dan Coach melihat linimasa riwayat revisi | Membuka detail tugas / video yang pernah direvisi | Riwayat revisi tampil kronologis lengkap dengan nama Coach, tanggal & jam (WIB), dan teks catatan. | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V09** | Mahasiswa mengirim ulang tautan video saat status `revision` | Memasukkan URL video baru pada form kirim ulang | Baris video yang sama ditimpa dengan URL baru, status kembali ke `pending`, dan **riwayat revisi sebelumnya tetap utuh tersimpan**. | **Lulus** (`VideoSubmissionTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V10** | Mahasiswa mencoba kirim ulang saat status masih `pending` atau sudah `approved` | Memaksa request update video yang sedang di-review atau sudah disetujui | Server menolak permintaan dengan pesan error bahwa kirim ulang hanya diizinkan saat status revisi. | **Lulus** (`VideoSubmissionTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V11** | Coach lain membuka atau mereview video di luar proyek miliknya | Mengakses halaman review video proyek milik Coach lain | Server menolak akses dengan HTTP **403 Forbidden**. | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |
| **BB-V12** | Integritas transaksi (*Database Transaction Atomicity*) | Simulasi kegagalan server di tengah proses approval | `DB::transaction` melakukan rollback penuh sehingga tidak terjadi ketidaksinkronan data antara video dan tugas. | **Lulus** (`VideoReviewTest`) | [ISI OLEH PENULIS] | [ISI] |

---

## 3. Rangkuman Hasil Automated Unit & Feature Testing

Automated testing dijalankan menggunakan PHPUnit 11 pada kerangka Laravel Testing Environment dengan basis data pengujian SQLite in-memory untuk menjamin isolasi dan kecepatan uji.

- **Total Pengujian**: **143 Pengujian (Test Cases)**
- **Total Pernyataan Evaluasi**: **495 Asersi (Assertions)**
- **Hasil Akhir**: **100% Lulus (0 Gagal, 0 Eror)**
- **Durasi Rata-rata**: ~9-10 detik.

```
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Unit\TaskModelTest (2 tests)
   PASS  Tests\Unit\ValidVideoUrlRuleTest (5 tests)
   PASS  Tests\Unit\VideoEmbedServiceTest (21 tests)
   PASS  Tests\Feature\AdminUserTest (11 tests)
   PASS  Tests\Feature\CoachProjectTest (12 tests)
   PASS  Tests\Feature\CoachTaskTest (8 tests)
   PASS  Tests\Feature\DashboardTest (5 tests)
   PASS  Tests\Feature\HandoverTest (13 tests)
   PASS  Tests\Feature\MahasiswaTaskTest (10 tests)
   PASS  Tests\Feature\ProfileTest (4 tests)
   PASS  Tests\Feature\RoleAccessTest (11 tests)
   PASS  Tests\Feature\VideoReviewTest (11 tests)
   PASS  Tests\Feature\VideoSubmissionTest (11 tests)
   PASS  Tests\Feature\Auth\AuthenticationTest (5 tests)
   PASS  Tests\Feature\Auth\EmailVerificationTest (3 tests)
   PASS  Tests\Feature\Auth\PasswordConfirmationTest (3 tests)
   PASS  Tests\Feature\Auth\PasswordResetTest (4 tests)
   PASS  Tests\Feature\ExampleTest (1 test)
   --------------------------------------------------------
   Tests:    143 passed (495 assertions)
```

---

## 4. Panduan & Instrumen Pengujian Pengguna (User Acceptance Testing)

Pengujian pengguna melibatkan peserta magang di lingkungan PT Teknologi Server Indonesia atau rekan mahasiswa D3 Teknik Informatika yang memerankan peran Coach dan Mahasiswa.

### 4.1 Lembar Skenario Uji Peran Coach
Instruksikan penguji peran Coach untuk menjalankan langkah-langkah berikut:
1. **Langkah 1**: Login menggunakan akun Coach (`coach@example.com` / `password123`).
2. **Langkah 2**: Periksa tampilan dasbor (ringkasan statistik proyek, antrean video pending, dan kartu tugas).
3. **Langkah 3**: Buka menu "Proyek Saya", lalu buka detail proyek magang yang telah ada.
4. **Langkah 4**: Buka tab "Dokumen Handover", buat dokumen teks baru atau unduh dokumen yang tersedia.
5. **Langkah 5**: Buka menu "Review Video", pilih video berstatus *Pending*, putar video lewat player, kemudian berikan revisi dengan catatan yang konstruktif.
6. **Langkah 6**: Pada video yang sudah diperbaiki, berikan persetujuan (*Approve*) dan amati apakah status tugas otomatis berubah menjadi selesai (*Done*).

### 4.2 Lembar Skenario Uji Peran Mahasiswa
Instruksikan penguji peran Mahasiswa untuk menjalankan langkah-langkah berikut:
1. **Langkah 1**: Login menggunakan akun Mahasiswa (`mahasiswa@example.com` / `password123`).
2. **Langkah 2**: Periksa dasbor mahasiswa (daftar tugas aktif terurut tenggat waktu dan link langsung ke dokumen handover proyek).
3. **Langkah 3**: Buka detail tugas, unduh/baca dokumen handover teknis proyek.
4. **Langkah 4**: Ubah status tugas ke *In Progress*, sesuaikan persentase progres, dan tuliskan catatan kemajuan.
5. **Langkah 5**: Masukkan tautan video dokumentasi YouTube / Google Drive pada form kirim video.
6. **Langkah 6**: Jika menerima catatan revisi dari Coach, periksa linimasa revisi dan lakukan pengiriman ulang video perbaikan.

---

### 4.3 Instrumen Kuesioner Evaluasi (System Usability Scale — SUS)

Kuesioner SUS menggunakan 10 butir pertanyaan standar dengan skala Likert 1 (Sangat Tidak Setuju) sampai 5 (Sangat Setuju).

| No | Pernyataan Kuesioner SUS | Nilai Jawaban (1 - 5) |
|:--:|:---|:---:|
| 1 | Saya merasa ingin sering menggunakan sistem SIMHOP ini dalam kegiatan magang. | `[1 - 5]` |
| 2 | Saya merasa antarmuka dan alur sistem ini terlalu rumit untuk dipahami. | `[1 - 5]` |
| 3 | Saya merasa sistem ini sangat mudah untuk digunakan dalam proses handover dan review. | `[1 - 5]` |
| 4 | Saya merasa membutuhkan bantuan dari orang lain atau tim teknis untuk dapat mengoperasikan sistem ini. | `[1 - 5]` |
| 5 | Saya merasa fitur-fitur pada sistem (handover, progres tugas, player video) terintegrasi dengan sangat baik. | `[1 - 5]` |
| 6 | Saya merasa ada terlalu banyak hal yang tidak konsisten pada sistem ini. | `[1 - 5]` |
| 7 | Saya membayangkan bahwa kebanyakan orang akan dapat mempelajari sistem ini dengan sangat cepat. | `[1 - 5]` |
| 8 | Saya merasa sistem ini membingungkan atau canggung saat digunakan. | `[1 - 5]` |
| 9 | Saya merasa sangat percaya diri saat mengoperasikan sistem ini. | `[1 - 5]` |
| 10 | Saya perlu membiasakan diri terlebih dahulu dengan banyak hal sebelum saya dapat menggunakan sistem ini secara mandiri. | `[1 - 5]` |

#### Rumus Perhitungan Skor SUS:
1. Untuk butir bernomor **ganjil** (1, 3, 5, 7, 9): skor kontribusi = $\text{Nilai Jawaban} - 1$.
2. Untuk butir bernomor **genap** (2, 4, 6, 8, 10): skor kontribusi = $5 - \text{Nilai Jawaban}$.
3. Jumlahkan seluruh skor kontribusi (rentang 0 - 40), lalu kalikan dengan **2,5** untuk mendapatkan total skor SUS akhir (rentang 0 - 100).

#### Interpretasi Skor Rata-rata SUS:
- **Skor > 80,3**: Kategori *Grade A (Excellent)* — Sangat diterima dengan baik (*Acceptable*).
- **Skor 68 - 80,3**: Kategori *Grade B/C (Good)* — Di atas rata-rata industri (*Above Average*).
- **Skor 51 - 67**: Kategori *Grade D (OK)* — Perlu sedikit perbaikan pada antarmuka.
- **Skor < 51**: Kategori *Grade F (Poor)* — Tidak dapat diterima (*Not Acceptable*).

---

## 5. Daftar Tangkapan Layar (Screenshots) untuk Lampiran Bab IV

Untuk memudahkan kelengkapan gambar antarmuka pada Bab IV dan Lampiran Laporan TA, berikut daftar halaman yang siap diambil tangkapan layarnya:

1. **Halaman Autentikasi**:
   - `Gambar 4.1` Antarmuka Halaman Login Berbasis Peran SIMHOP.
   - `Gambar 4.2` Tampilan Validasi Penolakan Akun Nonaktif.
2. **Antarmuka Admin**:
   - `Gambar 4.3` Dasbor Administrator (Metrik Pengguna per Role & Monitoring Akun Nonaktif).
   - `Gambar 4.4` Halaman Manajemen Pengguna (Daftar Pengguna & Fitur Toggle Aktif/Nonaktif).
   - `Gambar 4.5` Formulir Tambah dan Edit Pengguna (Validasi Email Unik dan Wajib NIM).
3. **Antarmuka Coach**:
   - `Gambar 4.6` Dasbor Coach (Kartu Antrean Review Video & Quick Action).
   - `Gambar 4.7` Halaman Detail Proyek dan Manajemen Anggota Tim Pemagang.
   - `Gambar 4.8` Formulir Tambah/Edit Tugas dengan Pemilihan Penerima Anggota Tim.
   - `Gambar 4.9` Tab Dokumen Handover (Form Teks & Unduhan Berkas Privat).
   - `Gambar 4.10` Halaman Antrean dan Riwayat Review Video Dokumentasi.
   - `Gambar 4.11` Halaman Detail Review Video (Embedded Player, Linimasa Revisi, Tombol Setujui & Minta Revisi).
4. **Antarmuka Mahasiswa**:
   - `Gambar 4.12` Dasbor Mahasiswa (Daftar Tugas Aktif Terurut Tenggat & Badge Status Video).
   - `Gambar 4.13` Halaman Detail Tugas & Pembaruan Progres (Slider Progres & Catatan).
   - `Gambar 4.14` Formulir Pengiriman Tautan Video Dokumentasi YouTube / Google Drive.
   - `Gambar 4.15` Tampilan Linimasa Catatan Revisi dari Coach dan Formulir Kirim Ulang.
   - `Gambar 4.16` Tampilan Dokumen Handover Spesifikasi Teknis Proyek.
5. **Halaman Penanganan Akses & Error**:
   - `Gambar 4.17` Halaman Kustom Kesalahan 403 (Akses Ditolak).
   - `Gambar 4.18` Halaman Kustom Kesalahan 404 (Halaman Tidak Ditemukan).
