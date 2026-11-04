# TESTING — Strategi Pengujian SIMHOP

Dua jenis pengujian: **(A) tes otomatis** (PHPUnit/Pest, dijalankan agent dan pemilik) dan **(B) black box testing manual** (data untuk Bab IV, dijalankan dan dicatat pemilik/penguji).

## A. Tes otomatis

Jalankan: `php artisan test`. Konfigurasi bawaan memakai SQLite in-memory.

### A.1 Status suite tes otomatis (142 tes, 490 assertions — Seluruhnya Hijau)
| Kelas | Isi | Status |
|---|---|---|
| `Unit/VideoEmbedServiceTest` | 11 URL valid (YouTube watch/youtu.be/embed/shorts, tanpa skema, Drive view/multi-akun/open/uc) dan 10 URL tidak valid (host mirip, `javascript:`, ID pendek, folder Drive, `v[]` array) | Selesai & Hijau (21 tes) |
| `Unit/ValidVideoUrlRuleTest` | Pengujian Rule `ValidVideoUrl` (YouTube valid, Drive valid, host terlarang, malformed URL, tipe non-string) | Selesai & Hijau (5 tes) |
| `Unit/TaskModelTest` | Pengujian method `Task::markAsDone()` (status done, progress 100%) dan konstanta status task | Selesai & Hijau (2 tes) |
| `Feature/RoleAccessTest` | `/register` 404, redirect per role, 403 lintas role, tamu ke login, akun nonaktif keluar, halaman error 403 & 404 kustom | Selesai & Hijau (10 tes) |
| `Feature/AdminUserTest` | CRUD pengguna, toggle aktif/nonaktif, pencegahan nonaktifkan diri sendiri / admin terakhir / ubah role sendiri | Selesai & Hijau (11 tes) |
| `Feature/CoachProjectTest` | CRUD proyek coach, validasi tanggal, manajemen anggota tim, proteksi kepemilikan | Selesai & Hijau (12 tes) |
| `Feature/CoachTaskTest` | CRUD tugas coach, batasan penerima harus anggota tim, proteksi kepemilikan | Selesai & Hijau (8 tes) |
| `Feature/MahasiswaTaskTest` | Lihat tugas sendiri, pembaruan status dan progres mahasiswa, larangan set done sendiri | Selesai & Hijau (10 tes) |
| `Feature/HandoverTest` | CRUD dokumen teks & berkas handover (max 10MB, mime filter), download anggota, proteksi non-anggota | Selesai & Hijau (13 tes) |
| `Feature/VideoSubmissionTest` | Pengiriman tautan video YouTube/Drive, validasi embed, resubmit hanya saat status revisi | Selesai & Hijau (11 tes) |
| `Feature/VideoReviewTest` | Review coach, approve video (+task done 100%), minta revisi (+catatan), rollback transaksi atomik | Selesai & Hijau (11 tes) |
| `Feature/DashboardTest` | Dasbor nyata Admin, Coach, Mahasiswa tanpa N+1 query, redirect dan proteksi lintas role | Selesai & Hijau (5 tes) |
| `Feature/Auth/*` | Otentikasi login/logout (termasuk penolakan akun nonaktif), reset password, konfirmasi password, profil (larangan hapus akun sendiri) | Selesai & Hijau (23 tes) |

### A.2 Tes yang harus ditulis per tugas
| Tugas | Kelas | Skenario minimal | Status |
|---|---|---|---|
| T-00/T-01 | `CoachProjectTest` | buat proyek; validasi `end_date`; tambah/keluarkan anggota; coach lain 403; mahasiswa 403 | Selesai |
| T-01 | `CoachTaskTest` | buat tugas; penerima bukan anggota ditolak; ubah; hapus; coach lain 403 | Selesai |
| T-02 | `MahasiswaTaskTest` | lihat hanya tugas sendiri; ubah progres/status; tidak bisa set `done`; tugas orang lain 403 | Selesai |
| T-03 | `HandoverTest` | buat dokumen; berkas >10 MB ditolak; tipe terlarang ditolak; anggota dapat unduh; non-anggota 403 | Selesai |
| T-04 | `VideoSubmissionTest` | kirim video valid; URL tidak valid ditolak; hanya tugas sendiri; kirim ulang hanya saat `revision` | Selesai |
| T-05 | `VideoReviewTest` | approve (status approved + tugas done); revisi tanpa catatan ditolak; revisi sukses (revisions + status revision); transaksi atomik; coach lain 403 | Selesai |
| T-06 | `AdminUserTest` | buat/ubah/nonaktifkan; tidak bisa nonaktifkan diri sendiri; non-admin 403 | Selesai |
| T-07 | `DashboardTest` | dasbor ringkasan tiap role dengan data nyata dan proteksi akses | Selesai |
| T-08 | `AuthenticationTest`, `ProfileTest`, `RoleAccessTest` | tolak login akun nonaktif, tolak hapus akun sendiri 403, penanganan halaman error 403 & 404 | Selesai |
| T-09 | `ValidVideoUrlRuleTest`, `TaskModelTest` | pengujian rule URL video dan method model `markAsDone()` | Selesai |

Aturan: tes memakai `RefreshDatabase` dan factory. Perbarui `UserFactory` agar menerima `role` dan `is_active` (default `mahasiswa`, `true`).

## B. Black Box Testing (untuk Bab IV)

Format tabel yang dipakai (isi kolom **Hasil Aktual**, **Status**, penguji, dan tanggal oleh pemilik; agent tidak boleh mengisi hasil yang belum diuji):

| ID | Modul | Skenario | Data Masukan | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|

### Kasus uji awal (hasil yang diharapkan sudah ditetapkan)

**Autentikasi**
| ID | Skenario | Masukan | Diharapkan |
|---|---|---|---|
| BB-A01 | Login benar (tiap role) | email+password valid | Masuk dasbor sesuai role |
| BB-A02 | Login salah | password salah | Pesan error, tetap di login |
| BB-A03 | Akun nonaktif | akun `is_active=false` | Dikeluarkan, pesan "Akun Anda dinonaktifkan" |
| BB-A04 | Buka `/register` | - | 404 |
| BB-A05 | Mahasiswa buka `/admin/dashboard` | - | 403 |
| BB-A06 | Tamu buka halaman terlindungi | - | Dialihkan ke login |
| BB-A07 | Logout | klik logout | Sesi berakhir |

**Admin**
| ID | Skenario | Diharapkan |
|---|---|---|
| BB-U01 | Tambah pengguna (tiap role) | Tersimpan, dapat login |
| BB-U02 | Email duplikat | Pesan validasi |
| BB-U03 | Nonaktifkan akun lain | Akun tidak bisa login |
| BB-U04 | Nonaktifkan akun sendiri | Ditolak |

**Proyek dan tugas (Coach)**
| ID | Skenario | Diharapkan |
|---|---|---|
| BB-P01 | Buat proyek valid | Tersimpan, diarahkan ke detail |
| BB-P02 | Tanggal selesai < mulai | Pesan validasi |
| BB-P03 | Tambah mahasiswa ke proyek | Muncul di anggota, hilang dari dropdown |
| BB-P04 | Keluarkan mahasiswa | Hilang dari anggota |
| BB-P05 | Buka proyek coach lain via URL | 403 |
| BB-T01 | Buat tugas dengan penerima anggota | Tersimpan status `todo` |
| BB-T02 | Penerima non-anggota | Ditolak |
| BB-T03 | Ubah dan hapus tugas | Berhasil, hapus dengan konfirmasi |

**Handover**
| ID | Skenario | Diharapkan |
|---|---|---|
| BB-H01 | Buat dokumen teks | Tersimpan, tampil bagi anggota |
| BB-H02 | Unggah aset <= 10 MB | Berhasil |
| BB-H03 | Unggah aset > 10 MB | Ditolak dengan pesan |
| BB-H04 | Unggah tipe terlarang (.exe/.php) | Ditolak |
| BB-H05 | Mahasiswa anggota mengunduh | Berhasil |
| BB-H06 | Non-anggota membuka tautan unduh | 403 |

**Tugas (Mahasiswa)**
| ID | Skenario | Diharapkan |
|---|---|---|
| BB-M01 | Lihat tugas sendiri | Hanya tugas miliknya |
| BB-M02 | Ubah ke `in_progress`, isi progres 50 | Tersimpan |
| BB-M03 | Coba set `done` | Tidak tersedia/ditolak |
| BB-M04 | Progres di luar 0-100 | Pesan validasi |

**Video dan review**
| ID | Skenario | Diharapkan |
|---|---|---|
| BB-V01 | Kirim tautan YouTube valid | Tersimpan `pending`, embed terbentuk |
| BB-V02 | Kirim tautan Drive valid | Tersimpan, embed `/preview` |
| BB-V03 | Kirim tautan situs lain/ngawur | Pesan validasi |
| BB-V04 | Coach membuka video | Player memutar di halaman |
| BB-V05 | Coach setujui | Status `approved`, tugas `done` |
| BB-V06 | Minta revisi tanpa catatan | Ditolak |
| BB-V07 | Minta revisi dengan catatan | Revisi tersimpan, status `revision`, tugas tetap `in_progress` |
| BB-V08 | Mahasiswa melihat riwayat revisi | Kronologis, bernama coach dan tanggal |
| BB-V09 | Kirim ulang saat `revision` | Baris sama ditimpa, status `pending`, riwayat utuh |
| BB-V10 | Kirim ulang saat `pending`/`approved` | Ditolak |
| BB-V11 | Coach lain membuka video | 403 |

### Pengujian oleh pengguna
- Peserta: pemagang yang memerankan Coach dan Mahasiswa. **Catat jujur** siapa, peran, dan tanggal. Jangan menyebut coach/pimpinan perusahaan menguji bila tidak.
- Instrumen opsional: lembar skenario (3-5 tugas) dan kuesioner kegunaan (mis. SUS, 10 butir skala 1-5). Hasil diisi pemilik dari data nyata.
- Sertakan keterbatasan: penguji bukan coach sesungguhnya, jumlah sampel kecil.
