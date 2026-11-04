# AGENTS.md — Panduan Wajib untuk AI Agent (Proyek SIMHOP)

> Berkas ini dibaca PERTAMA oleh setiap AI agent (Claude Code, Gemini CLI, Copilot, Cursor, dll.).
> Untuk agent tertentu, salin berkas ini sebagai `CLAUDE.md` atau `GEMINI.md` di akar proyek bila diperlukan.

## 1. Apa proyek ini

**SIMHOP** (Sistem Informasi Monitoring dan Handover Proyek Magang) adalah aplikasi web Laravel 12 untuk Tugas Akhir D3 Teknik Informatika, Politeknik Negeri Cilacap.
Judul TA: *"Rancang Bangun Sistem Informasi Monitoring dan Handover Proyek Magang Berbasis Framework Laravel"*.

Dua masalah nyata yang diselesaikan (dialami penulis saat magang di PT Teknologi Server Indonesia, Yogyakarta):

1. **Handover proyek tidak efisien**: serah terima dilakukan dengan mengunduh image OS Ubuntu berukuran besar, dokumentasi teknis minim.
2. **Monitoring dan review buruk**: peserta magang mengunggah video dokumentasi ke Google Drive pribadi lalu mengirim tautannya lewat WhatsApp ke coach; tautan tenggelam, coach lambat merespons, dan catatan revisi berupa chat yang tidak terikat pada video.

Solusi: modul **Handover** (dokumentasi teknis terpusat dan ringan) dan modul **Monitoring and Video Review** (kirim tautan video, putar lewat embedded player, approve atau revisi dengan catatan terikat pada video).

## 2. Urutan membaca dokumen (wajib)

| Urutan | Berkas | Isi |
|---|---|---|
| 1 | `AGENTS.md` | Aturan keras (berkas ini di akar proyek) |
| 2 | `simhop-docs/PRD.md` | Tujuan, pengguna, kebutuhan fungsional, batas lingkup |
| 3 | `simhop-docs/BUSINESS_RULES.md` | Aturan bisnis, state machine, matriks hak akses |
| 4 | `simhop-docs/DATABASE.md` | Skema 7 tabel final dan relasi |
| 5 | `simhop-docs/ARCHITECTURE.md` | Struktur kode, rute, alur, keamanan |
| 6 | `simhop-docs/GUIDELINES.md` | Konvensi penulisan kode dan UI |
| 7 | `simhop-docs/DECISIONS.md` | Keputusan yang sudah disepakati (jangan dibalik) |
| 8 | `simhop-docs/TESTING.md` | Strategi pengujian dan kasus uji |
| 9 | `simhop-docs/TASKS.md` | Status pekerjaan dan backlog berurutan |

Kerjakan **satu tugas dari `simhop-docs/TASKS.md` per sesi**, sesuai urutan dan dependensinya.

## 3. Aturan keras (NEVER / ALWAYS)

### Keputusan produk (tidak boleh dilanggar)
- **NEVER** membuat pendaftaran publik (`/register` harus tetap 404). Semua akun dibuat Admin.
- **NEVER** membuat fitur notifikasi, tabel `notifications`, chat, atau penilaian akhir magang.
- **NEVER** menyimpan berkas video di server. Hanya `video_url` dan `embed_url` (YouTube/Google Drive).
- **NEVER** merender URL buatan pengguna langsung ke `<iframe>`. Selalu lewat `VideoEmbedService`.
- **NEVER** membalik keputusan di `simhop-docs/DECISIONS.md` atau aturan di `simhop-docs/BUSINESS_RULES.md`. Jika instruksi pengguna bertabrakan, **berhenti dan tanyakan** sebelum menulis kode.
- **ALWAYS** menjaga hanya 3 role: `admin`, `coach`, `mahasiswa`.

### Database
- **NEVER** mengedit migrasi yang sudah dijalankan. Perubahan skema = migrasi baru dengan timestamp lebih baru.
- **NEVER** menambah tabel atau kolom di luar `simhop-docs/DATABASE.md` tanpa mencatat keputusan baru di `simhop-docs/DECISIONS.md` dan persetujuan pemilik proyek.
- **NEVER** menjalankan `migrate:fresh`, `migrate:rollback`, `db:wipe`, atau perintah penghapus data tanpa konfirmasi eksplisit pemilik. (`migrate:fresh --seed` aman di lingkungan dev lokal, tetapi tetap beri tahu dulu.)

### Keamanan dan kualitas
- **ALWAYS** validasi input lewat `FormRequest` (atau `$request->validate()` untuk kasus sangat kecil). **NEVER** pakai `$request->all()` untuk `create()`/`update()`.
- **ALWAYS** cek otorisasi kepemilikan (coach hanya proyek miliknya, mahasiswa hanya tugas dan video miliknya).
- **ALWAYS** bungkus operasi yang mengubah lebih dari satu tabel dengan `DB::transaction`.
- **ALWAYS** eager loading (`with()`) untuk mencegah N+1 pada daftar.
- **NEVER** memasang package baru, mengubah versi PHP/Laravel, atau mengganti Breeze/Tailwind tanpa izin.
- **NEVER** menaruh rahasia (password nyata, API key) di kode atau commit. Password `password123` hanya untuk akun seeder dev.

### Kejujuran kerja
- **NEVER** menyatakan sebuah fitur "selesai" atau "berfungsi" tanpa benar-benar menjalankan verifikasinya (`php artisan test`, `php artisan route:list`, atau pemeriksaan manual yang dijelaskan). Laporkan **perintah yang dijalankan dan hasil sebenarnya**. Jika tidak bisa menjalankan sesuatu, katakan terus terang.
- **NEVER** mengarang sitasi, nama jurnal, kutipan, data pengujian, hasil kuesioner, atau klaim dampak pada dokumen akademik (Bab I-V). Tandai dengan `[ISI OLEH PENULIS]` bila data belum ada.
- Kode yang berasal dari asisten AI sebelumnya **belum pernah dieksekusi oleh asistennya sendiri**. Hasil uji pemilik proyek di komputernya adalah acuan kebenaran. Verifikasi, jangan asumsikan.

## 4. Lingkungan

- OS pengembang: **Windows**, shell **PowerShell**, lingkungan **Laragon** (PHP 8.4, MySQL, Composer, Node).
- Folder proyek: `D:\simhop`. Database: `simhop_db` (MySQL, user `root`, tanpa password).
- Jalankan: `php artisan serve` (http://127.0.0.1:8000) dan `npm run dev` di terminal terpisah.
- Perintah Windows: gunakan sintaks PowerShell (`Remove-Item`, `dir`), bukan `rm`/`ls` Linux, kecuali memang tersedia.
- Docker **bukan** bagian dari lingkungan sistem ini (hanya konteks studi kasus perusahaan).
- Peringatan `Module "zip" is already loaded` saat menjalankan `php`/`artisan` itu tidak berbahaya, abaikan.

## 5. Perintah yang sering dipakai

```
php artisan route:list --name=coach          # cek rute
php artisan migrate                           # jalankan migrasi baru
php artisan migrate:fresh --seed              # reset dev (TANYA DULU)
php artisan test                              # seluruh tes
php artisan test --filter=NamaTes             # satu tes
php artisan optimize:clear                    # bersihkan cache
php artisan tinker                            # uji manual model
```

Akun seeder (dev saja): `admin@example.com`, `coach@example.com` (Coach Uji), `coach2@example.com` (Coach Dua), `mahasiswa@example.com` (Mahasiswa Uji, NIM 2024001), semuanya `password123`.

## 6. Cara bekerja

1. Baca `simhop-docs/TASKS.md`, pilih tugas berikutnya yang dependensinya sudah selesai.
2. Baca aturan terkait di `simhop-docs/BUSINESS_RULES.md`, lalu ikuti pola kode yang **sudah ada** di codebase (lihat `simhop-docs/GUIDELINES.md`).
3. Rencanakan singkat (berkas apa yang dibuat/diubah), lalu implementasikan dengan perubahan sekecil mungkin.
4. Tulis atau perbarui tes (`simhop-docs/TESTING.md`).
5. Jalankan `php artisan test` dan verifikasi rute/tampilan yang relevan.
6. Perbarui status di `simhop-docs/TASKS.md` dan, bila ada keputusan baru, `simhop-docs/DECISIONS.md`.
7. Laporkan: apa yang diubah, perintah verifikasi + hasilnya, asumsi yang dibuat, dan hal yang butuh keputusan pemilik.

### Kapan WAJIB bertanya sebelum lanjut
- Instruksi bertabrakan dengan aturan di dokumen ini.
- Perlu tabel/kolom/package baru.
- Ada beberapa tafsir bisnis yang masuk akal (tandai pada bagian **ASUMSI** di `simhop-docs/BUSINESS_RULES.md`).
- Perlu tindakan destruktif.

## 7. Definition of Done (setiap tugas)

- [ ] Sesuai `simhop-docs/BUSINESS_RULES.md` dan tidak melanggar `simhop-docs/DECISIONS.md`
- [ ] Validasi lewat FormRequest, otorisasi kepemilikan, transaksi bila multi-tabel
- [ ] Teks UI dalam Bahasa Indonesia, memakai komponen Breeze/Tailwind yang sudah ada
- [ ] Tes fitur untuk jalur sukses dan jalur gagal/403 ditulis dan lulus
- [ ] `php artisan test` lulus; tidak ada tes lama yang dirusak
- [ ] `simhop-docs/TASKS.md` diperbarui; laporan jujur diberikan ke pemilik proyek

## Aturan Pekerjaan UI/UX

- Skill desain (ui-ux-pro-max, frontend-design) hanya dipakai untuk TAMPILAN. Jangan mengubah logika bisnis, rute, validasi, model, atau migrasi.
- Tetap memakai Blade + Tailwind (Vite) + komponen Breeze. Dilarang menambah framework JS (Vue/React/Livewire) atau package baru tanpa izin pemilik.
- Simpan design system di berkas (mis. tokens warna/tipografi di tailwind config) dan pakai konsisten di semua halaman.
- Semua teks UI tetap Bahasa Indonesia. Dukungan dark mode yang sudah ada tidak boleh dirusak.
- Setiap perubahan tampilan harus tetap lolos `php artisan test`.
- Jangan mengubah nama rute, nama field form, atau nama variabel yang dipakai controller.
- Hasil desain yang dibuat harus dicek aksesibilitas dasar: kontras warna, label form, fokus keyboard.