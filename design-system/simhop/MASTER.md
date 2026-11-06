# Design System Master File — SIMHOP

> **LOGIC:** Ketika membangun atau memperbarui tampilan halaman tertentu, pertama periksa `design-system/simhop/pages/[halaman].md`.
> Jika berkas tersebut ada, aturan di dalamnya **menimpa** berkas MASTER ini.
> Jika tidak ada, ikuti seluruh aturan baku di bawah ini secara ketat.

---

## Batasan Proyek (Menimpa Rekomendasi Generator)

> [!IMPORTANT]
> Aturan pada bagian ini adalah **aturan keras proyek SIMHOP** yang menimpa hasil rekomendasi otomatis dari tool generator.

1. **Palet Warna Dikunci (Resmi SIMHOP)**:
   - **Netral Canvas**: Mode Terang `bg-slate-50` / `bg-slate-100`, Mode Gelap `dark:bg-slate-900` (`#0F172A`).
   - **Netral Surface / Kartu**: Mode Terang `bg-white`, Mode Gelap `dark:bg-slate-800` (`#1E293B`).
   - **Border & Divider**: Mode Terang `border-slate-200` atau `border-slate-200/80`, Mode Gelap `dark:border-slate-700` atau `dark:border-slate-700/80`.
   - **Teks Utama / Headings**: Mode Terang `text-slate-900` (`#0F172A`, rasio kontras 17.85:1 pada putih, 16.30:1 pada slate-100), Mode Gelap `dark:text-slate-100` (`#F1F5F9`, rasio kontras 13.35:1 pada kartu slate-800, 16.30:1 pada canvas slate-900).
   - **Teks Sekunder**: Mode Terang `text-slate-700` (10.35:1 pada putih), Mode Gelap `dark:text-slate-300` (`#CBD5E1`, rasio kontras 9.85:1 pada kartu slate-800).
   - **Teks Redup / Helper / Metadata**: 
     - **Mode Terang**: Wajib memakai `text-slate-600` (`#475569`, rasio kontras 7.58:1 pada putih, 7.24:1 pada slate-50, dan 6.92:1 pada canvas slate-100 — **lolos WCAG AA >= 4.5:1**). 
     - *PERINGATAN ATURAN BARU*: `slate-500` di atas kanvas `slate-100` hanya menghasilkan kontras **4.34:1 (GAGAL)**. Oleh karena itu, `text-slate-500` **DILARANG** untuk teks normal di mode terang.
     - **Mode Gelap**: Wajib memakai `dark:text-slate-400` (`#94A3B8`, rasio kontras 5.71:1 pada kartu slate-800 dan 6.96:1 pada canvas slate-900 — **lolos WCAG AA >= 4.5:1**). `dark:text-slate-500` di mode gelap menghasilkan rasio 3.07:1 (gagal) sehingga dilarang untuk teks.
   - **Brand SIMHOP**: Biru SIMHOP `bg-blue-600` (5.17:1 dengan teks putih).
     - *Hover State*: Wajib memakai `hover:bg-blue-700` (6.70:1 dengan teks putih). `hover:bg-blue-500` **DILARANG** karena kontrasnya hanya 3.68:1 (gagal).
     - *Active State*: `active:bg-blue-800` (8.72:1 dengan teks putih).
     - Teks aksen: `text-blue-600 dark:text-blue-400`.
   - **Ring Fokus Aksesibilitas**: `focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900`.
   - **Semantic Status**:
     - `approved` / `completed`: Emerald terang `bg-emerald-100 text-emerald-800` (6.78:1) | gelap `dark:bg-emerald-950/60 dark:text-emerald-300` (9.60:1).
     - `pending`: Amber terang `bg-amber-100 text-amber-900` (8.15:1) | gelap `dark:bg-amber-950/60 dark:text-amber-300` (10.15:1).
     - `in_progress`: Sky terang `bg-sky-100 text-sky-900` (8.24:1) | gelap `dark:bg-sky-950/60 dark:text-sky-300` (8.77:1).
     - `revision`: Orange terang `bg-orange-100 text-orange-900` (8.18:1) | gelap `dark:bg-orange-950/60 dark:text-orange-300` (8.67:1).
     - `danger` / Bahaya / Error: Rose terang `bg-rose-100 text-rose-800` (6.68:1) | gelap `dark:bg-rose-950/60 dark:text-rose-300` (7.74:1).
2. **Tipografi Dikunci**:
   - Hanya memakai font `Figtree` (`font-sans` bawaan Laravel Breeze/Tailwind).
   - Tidak menggunakan Google Fonts eksternal (Inter/lainnya).
3. **Ikon Dikunci**:
   - Hanya SVG inline murni dengan atribut ukuran eksplisit (`w-4 h-4` atau `w-5 h-5`), `fill="none"`, `stroke="currentColor"`, dan `aria-hidden="true"` untuk ikon dekoratif di samping teks.
   - Tidak ada paket ikon eksternal (tidak ada blade-icons, tidak ada heroicons npm, tidak ada phosphor npm).
4. **Teknologi & Tailwind v3 Compatibility**:
   - Proyek menggunakan Tailwind CSS `^3.1.0`. Seluruh kelas wajib kompatibel dengan Tailwind v3 (mis. gunakan `shadow-sm` alih-alih `shadow-xs`; gunakan `w-4 h-4` / `w-5 h-5` alih-alih `size-*`).
   - Murni Blade Templates + Tailwind CSS + Alpine.js (bawaan Breeze).
   - Tanpa GSAP runtime atau framework JS baru. Animasi hanya transisi CSS native Tailwind (`transition-colors duration-150`, `transition duration-200`).
5. **Bahasa Antarmuka**:
   - Seluruh teks UI, label, tombol, pesan sukses/kesalahan, dan tooltip wajib dalam **Bahasa Indonesia**.
6. **Dukungan Tema & Aksesibilitas**:
   - Mode gelap dan mode terang wajib didukung setara di setiap halaman dan komponen.
   - Target kontras teks normal minimal **4.5:1 (WCAG AA)** di kedua mode.
7. **Cakupan Platform**:
   - Web responsif: Desktop lebar, laptop/tablet (768px - 1024px), dan layar ponsel (~390px).
   - Abaikan seluruh aturan khusus App UI mobile-native.

---

## Tabel Kontras Terverifikasi (Rumus WCAG 2.1)

| Elemen / Pasangan Warna | Mode Terang | Rasio Terang | Mode Gelap | Rasio Gelap | Status WCAG (>= 4.5:1) |
|---|---|---|---|---|---|
| **Teks Utama pada Kartu** | `text-slate-900` di atas `bg-white` | **17.85:1** | `dark:text-slate-100` di atas `dark:bg-slate-800` | **13.35:1** | **PASS** (Sangat Tajam) |
| **Teks Utama pada Canvas** | `text-slate-900` di atas `bg-slate-100` | **16.30:1** | `dark:text-slate-100` di atas `dark:bg-slate-900` | **16.30:1** | **PASS** (Sangat Tajam) |
| **Label Form / Teks Tebal** | `text-slate-700` di atas `bg-white` | **10.35:1** | `dark:text-slate-200` di atas `dark:bg-slate-800` | **11.87:1** | **PASS** (Aman) |
| **Teks Sekunder** | `text-slate-700` di atas `bg-slate-50` | **9.90:1** | `dark:text-slate-300` di atas `dark:bg-slate-800` | **9.85:1** | **PASS** (Aman) |
| **Teks Redup / Helper / Meta** | `text-slate-600` di atas `bg-slate-100` | **6.92:1** | `dark:text-slate-400` di atas `dark:bg-slate-800` | **5.71:1** | **PASS** (Aman) |
| **Teks Redup pada Kartu** | `text-slate-600` di atas `bg-white` | **7.58:1** | `dark:text-slate-400` di atas `dark:bg-slate-900` | **6.96:1** | **PASS** (Aman) |
| **Tombol Utama (Normal)** | Teks putih di atas `bg-blue-600` | **5.17:1** | Teks putih di atas `bg-blue-600` | **5.17:1** | **PASS** |
| **Tombol Utama (Hover)** | Teks putih di atas `hover:bg-blue-700` | **6.70:1** | Teks putih di atas `dark:hover:bg-blue-700` | **6.70:1** | **PASS** |
| **Tombol Utama (Active)** | Teks putih di atas `active:bg-blue-800`| **8.72:1** | Teks putih di atas `dark:active:bg-blue-800`| **8.72:1** | **PASS** |
| **Badge Disetujui** | `text-emerald-800` di atas `bg-emerald-100` | **6.78:1** | `dark:text-emerald-300` di atas `dark:bg-slate-800` | **9.60:1** | **PASS** |
| **Badge Menunggu** | `text-amber-900` di atas `bg-amber-100` | **8.15:1** | `dark:text-amber-300` di atas `dark:bg-slate-800` | **10.15:1** | **PASS** |
| **Badge Dalam Pengerjaan** | `text-sky-900` di atas `bg-sky-100` | **8.24:1** | `dark:text-sky-300` di atas `dark:bg-slate-800` | **8.77:1** | **PASS** |
| **Badge Revisi** | `text-orange-900` di atas `bg-orange-100` | **8.18:1** | `dark:text-orange-300` di atas `dark:bg-slate-800` | **8.67:1** | **PASS** |
| **Badge Bahaya / Ditolak** | `text-rose-800` di atas `bg-rose-100` | **6.68:1** | `dark:text-rose-300` di atas `dark:bg-slate-800` | **7.74:1** | **PASS** |
| **Badge Draf / Belum Mulai**| `text-slate-700` di atas `bg-slate-100` | **9.45:1** | `dark:text-slate-300` di atas `dark:bg-slate-800` | **9.85:1** | **PASS** |
| **Placeholder Input** | `placeholder-slate-500` di atas `bg-white` | **4.76:1** | `dark:placeholder-slate-400` di atas `dark:bg-slate-900` | **6.96:1** | **PASS** |

> [!WARNING]
> Pasangan Warna Gagal yang DILARANG:
> - `slate-400` di atas latar putih (`bg-white`) = **2.56:1** (FAIL).
> - `slate-500` di atas canvas `slate-100` = **4.34:1** (FAIL).
> - `slate-500` di atas kartu `slate-800` = **3.07:1** (FAIL).
> - Teks putih di atas `blue-500` (hover lama) = **3.68:1** (FAIL).
> - Teks putih di atas `amber-600` = **3.19:1** (FAIL).
> - Teks putih di atas `orange-600` = **3.92:1** (FAIL).
> - Teks putih di atas `emerald-600` = **4.23:1** (FAIL).
> - `text-blue-600` tanpa varian dark di atas permukaan gelap (`dark:bg-slate-800`) = **2.83:1** (FAIL). Wajib berpasangan: `text-blue-600 dark:text-blue-400`.

---

## Token Mode Terang & Gelap (Kelas Tailwind v3)

| Token Desain | Mode Terang | Mode Gelap | Catatan Penggunaan |
|---|---|---|---|
| **Canvas Background** | `bg-slate-50` / `bg-slate-100` | `dark:bg-slate-900` | Latar belakang seluruh halaman |
| **Surface / Card** | `bg-white` | `dark:bg-slate-800` | Latar kartu, modal, popover, panel |
| **Card Border** | `border-slate-200/80` | `dark:border-slate-700/80` | Garis batas kartu dan kontainer |
| **Divider / Table Border**| `border-slate-200` | `dark:border-slate-700` | Pemisah baris tabel dan section |
| **Teks Utama / Heading** | `text-slate-900` | `dark:text-slate-100` | Judul halaman, nama entitas, teks body tebal |
| **Teks Sekunder** | `text-slate-700` | `dark:text-slate-300` | Paragraf penjelasan, label sekunder |
| **Teks Redup / Helper** | `text-slate-600` | `dark:text-slate-400` | Teks bantuan form, meta info, timestamp |
| **Brand Primary (Aksen)** | `bg-blue-600 hover:bg-blue-700 active:bg-blue-800` | `bg-blue-600 hover:bg-blue-700 active:bg-blue-800` | Tombol utama, tab aktif |
| **Focus Visible Ring** | `focus:ring-2 focus:ring-blue-500` | `dark:focus:ring-blue-400 dark:focus:ring-offset-slate-900` | Indikator keyboard accessibility |
| **Input Background** | `bg-white` | `dark:bg-slate-900` | Input teks, angka, textarea, select |
| **Input Border** | `border-slate-300` | `dark:border-slate-700` | Border netral input form |
| **Input Text** | `text-slate-900` | `dark:text-slate-100` | Nilai teks input yang diketik pengguna |
| **Input Placeholder** | `placeholder-slate-500` | `dark:placeholder-slate-400` | Kontras terverifikasi: 4.76:1 (terang) & 6.96:1 (gelap) |

---

## Spesifikasi Komponen

### 1. `x-status-badge`
* **Peran**: Menampilkan badge status seragam untuk tugas, proyek, dan video review.
* **Format**: Bentuk kapsul (*rounded-full*), ringkas, dengan titik warna indikator (*dot*), teks status Bahasa Indonesia, dan penanda semantik selain warna (dot + teks deskriptif).
* **Spesifikasi Kelas**:
  - Wrapper: `inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide whitespace-nowrap`
  - Titik (Dot): `w-1.5 h-1.5 rounded-full shrink-0`
  - Kelas per status:
    - `approved` / `completed`: `bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300`, dot: `bg-emerald-500 dark:bg-emerald-400`
    - `pending`: `bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300`, dot: `bg-amber-500 dark:bg-amber-400`
    - `in_progress`: `bg-sky-100 text-sky-900 dark:bg-sky-950/60 dark:text-sky-300`, dot: `bg-sky-500 dark:bg-sky-400`
    - `revision`: `bg-orange-100 text-orange-900 dark:bg-orange-950/60 dark:text-orange-300`, dot: `bg-orange-500 dark:bg-orange-400`
    - `not_started` / `draft`: `bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300`, dot: `bg-slate-400 dark:bg-slate-500`
    - `inactive`: `bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300`, dot: `bg-rose-500 dark:bg-rose-400`

### 2. `x-card`
* **Peran**: Kontainer permukaan utama untuk membungkus konten, tabel, formulir, atau rincian entitas.
* **Spesifikasi Kelas (Tailwind v3)**:
  - Wrapper: `rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-700/80 dark:bg-slate-800`
  - Header (opsional): Judul `text-base sm:text-lg font-semibold text-slate-900 dark:text-slate-100`, subjudul `text-xs text-slate-600 dark:text-slate-400 mt-0.5`, dan slot aksi kanan (`flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700/60`).
  - Body: Padding konten menyatu atau edge-to-edge untuk tabel (`-mx-5 -my-5 sm:-mx-6 sm:-my-6`).

### 3. `x-stat-card`
* **Peran**: Kartu ringkasan metrik KPI pada dashboard admin, coach, dan mahasiswa.
* **Spesifikasi Kelas**:
  - Wrapper: `rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-800 flex items-center justify-between`
  - Label atas: `text-xs font-medium uppercase tracking-wider text-slate-600 dark:text-slate-400`
  - Nilai angka utama: `mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100`
  - Keterangan bawah: `mt-1 text-xs text-slate-600 dark:text-slate-400`
  - Ikon wrapper: `w-11 h-11 rounded-lg flex items-center justify-center shrink-0`

### 4. Gaya Tombol (*Button Styles*)
* **Tombol Utama (`x-primary-button`)**:
  - Kelas: `inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-50 disabled:cursor-not-allowed`
* **Tombol Sekunder (`x-secondary-button`)**:
  - Kelas: `inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-50`
* **Tombol Solid Berwarna (Aksi Semantik & Antrean)**:
  - Wajib menggunakan latar varian `-700` dengan hover `-800` bila menggunakan teks putih agar kontras ≥ 4.5:1:
    - Amber (Antrean / Review): `bg-amber-700 hover:bg-amber-800 text-white shadow-sm` (5.12:1 - PASS).
    - Orange (Revisi): `bg-orange-700 hover:bg-orange-800 text-white shadow-sm` (5.71:1 - PASS).
    - Emerald (Persetujuan): `bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white shadow-sm` (6.00:1 - PASS).
    - Rose (Bahaya / Nonaktif): `bg-rose-700 hover:bg-rose-800 text-white shadow-sm` (7.20:1 - PASS).
* **Tombol Bahaya / Destruktif**:
  - Tindakan Ringan (Batal / Hapus baris tabel): `border border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors`.
  - Konfirmasi Modal Permanen: `bg-rose-700 hover:bg-rose-800 active:bg-rose-900 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-lg shadow-sm transition-colors focus:ring-rose-500`.

### 5. Aturan Aksesibilitas Elemen & Tautan
* **Tombol / Link Hanya-Ikon**:
  - Tombol hamburger menu wajib memiliki `:aria-expanded="open.toString()"`, `:aria-label="open ? 'Tutup menu navigasi' : 'Buka menu navigasi'"`, dan SVG di dalamnya wajib `aria-hidden="true"`.
  - Seluruh tombol kembali (*back button* ikon panah) wajib memiliki `aria-label` deskriptif berbahasa Indonesia (contoh: `aria-label="Kembali ke daftar proyek"`).
* **Tautan Berulang / Identik**:
  - Tautan dengan teks yang sama pada baris/kartu yang berbeda (misal "Review Sekarang →", "Detail Proyek") wajib menyertakan `aria-label` kontekstual yang menyebutkan nama entitas (contoh: `aria-label="Review video {{ $video->title }}"`).
* **Sintaks Shadow**:
  - Di Tailwind CSS v3, gunakan `shadow-sm` (bukan `shadow-xs` atau `shadow-2xs` yang merupakan fitur v4).

### 5. Formulir (Label, Input, Select, Textarea)
* **Label (`x-input-label`)**:
  - Kelas: `block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-200 mb-1` (kontras 10.35:1 terang, 11.87:1 gelap).
* **Input Teks, Angka, Password (`x-text-input`)**:
  - Kelas: `block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-500 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-blue-400 dark:focus:ring-blue-400/20 disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:text-slate-500`
* **Input Pilihan (`<select>`)**:
  - Kelas: `rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400`
* **Input Teks Panjang (`<textarea>`)**:
  - Kelas: `block w-full rounded-lg border border-slate-300 bg-white p-3 text-sm text-slate-900 placeholder-slate-500 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-blue-400 leading-relaxed`
* **Input Tanggal (`<input type="date">`)**:
  - Wajib menyertakan utilitas `dark:[color-scheme:dark]`.
* **Pesan Kesalahan (`x-input-error`)**:
  - Kelas: `text-xs sm:text-sm text-rose-600 dark:text-rose-400 mt-1.5 font-medium`
* **Teks Bantuan (Helper Text)**:
  - Kelas: `text-xs text-slate-600 dark:text-slate-400 mt-1` (kontras teruji 6.92:1 terang, 5.71:1 gelap).

### 6. Tabel Data (*Data Table*)
* **Kontainer**: Wajib dibungkus `<div class="overflow-x-auto">` untuk keamanan layar ponsel (~390px).
* **Tabel**: `min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-left text-sm`
* **Header (`<thead>`)**:
  - Baris: `bg-slate-50/75 dark:bg-slate-800/80`
  - Kolom th: `py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400`
* **Badan (`<tbody>`)**:
  - Pembagi baris: `divide-y divide-slate-200 dark:divide-slate-700`
  - Baris tr: `hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors`
  - Sel td: `py-3.5 px-4 text-slate-700 dark:text-slate-300 text-sm`

### 7. Keadaan Kosong (*Empty State*)
* **Format**:
  - Kontainer: `text-center py-10 px-4`
  - Ikon: Ikon garis SVG netral `mx-auto w-12 h-12 text-slate-400 dark:text-slate-500 mb-3`
  - Judul: `text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1`
  - Deskripsi: `text-xs text-slate-600 dark:text-slate-400 max-w-sm mx-auto mb-4`
  - Aksi: Tombol pemicu tindakan langsung.

---

## Tata Letak & Spacing Rhythm (Dashboard Density 8/10)

* **Kontainer Induk Halaman**: Selalu seragam `mx-auto max-w-7xl px-4 sm:px-6 lg:px-8` baik untuk `<x-slot name="header">` maupun isi utama halaman.
* **Skala Spacing (Density 8/10)**:
  - Gap komponen kecil / inline: `gap-1.5` (6px) atau `gap-2` (8px)
  - Padding kartu / kontainer: `p-5 sm:p-6` (20px - 24px)
  - Gap antar kartu / grid: `gap-4 sm:gap-6` (16px - 24px)
  - Jarak antar section vertikal: `space-y-6` (24px)
  - Padding vertikal halaman: `py-6 sm:py-8` (24px - 32px)

---

## Anti-Patterns (Dilarang Digunakan)

- ❌ **Emoji sebagai Ikon Navigasi atau Aksi** — Gunakan SVG inline.
- ❌ **Kontras Rendah di Mode Gelap & Terang** — Dilarang memakai `text-slate-500` pada kanvas `slate-100` mode terang (hanya 4.34:1) dan dilarang di mode gelap (3.07:1). Wajib gunakan `text-slate-600` di mode terang dan `dark:text-slate-400` di mode gelap.
- ❌ **Hover Tombol Biru 500** — Dilarang memakai `hover:bg-blue-500` karena rasio teks putih hanya 3.68:1. Wajib pakai `hover:bg-blue-700`.
- ❌ **Warna Hardcoded Acak** — Jangan memakai hex baru per file (`#123456`); patuhi token slate dan warna semantik resmi.
- ❌ **Pergeseran Layout Saat Hover** — Jangan memakai scale transform berlebihan (`hover:scale-105`) yang memicu jitter layout.
- ❌ **Lebar Kontainer Tidak Konsisten** — Jangan mencampur `max-w-5xl`, `max-w-6xl`, dan `max-w-7xl` antara header dan body dalam satu aplikasi.
- ❌ **Scroll Horizontal di Layar Ponsel (~390px)** — Pastikan tabel dibungkus `overflow-x-auto` dan formulir tidak menggunakan lebar statis (`w-[600px]`).

---

## Pre-Delivery Checklist (Wajib Dilaporkan Setiap Halaman)

Sebelum menyatakan sebuah halaman selesai diubah, agen wajib menguji dan melaporkan checklist berikut:

- [ ] **Visual**: Ikon berupa SVG inline, proporsional, tanpa emoji struktural; hierarki teks Figtree rapi dan terbaca.
- [ ] **Interaksi**: Efek hover dan active halus (150-200ms); `focus-visible` ring kontras dan jelas saat navigasi keyboard; tombol disabled terlihat jelas dan tidak dapat diklik.
- [ ] **Mode Gelap**: Kontras teks utama dan redup memenuhi minimal 4.5:1; input dan textarea memiliki latar gelap `dark:bg-slate-900` dengan teks terang dan border `dark:border-slate-700`; input tanggal memiliki `dark:[color-scheme:dark]`.
- [ ] **Mode Terang**: Latar canvas bersih (`bg-slate-50`/`bg-slate-100`), permukaan kartu putih (`bg-white`), teks terbaca tegas dan tajam (`text-slate-900`, `text-slate-600`).
- [ ] **Tata Letak (Layout)**: Lebar kontainer header dan body seragam (`max-w-7xl`); ritme padding dan gap konsisten (density 8/10).
- [ ] **Aksesibilitas**: Label form terikat dengan `for` dan `id`; pesan error jelas di bawah input; ikon dekoratif memiliki `aria-hidden="true"`.
- [ ] **Responsif Ponsel (~390px)**: Tidak ada luapan horizontal (*no horizontal scrollbar* pada body); tabel dapat digulir mandiri (*overflow-x-auto*); formulir tertata vertikal 1 kolom di layar sempit.
- [ ] **Tes Otomatis**: Seluruh suite `php artisan test` tetap 100% lulus (hijau).
