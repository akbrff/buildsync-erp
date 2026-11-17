# Panduan Arsitektur Frontend & Desain Visual BuildSync ERP

Dokumen ini ditujukan khusus bagi **UI/UX Designer** agar dapat memahami, mengelola, dan memodifikasi tampilan antarmuka (UI) BuildSync ERP secara mandiri tanpa harus berkutat dengan ratusan utility class Tailwind yang rumit di file HTML Blade.

---

## 1. Filosofi & Konsep Arsitektur

Sebelum refactoring ini dilakukan, hampir seluruh tampilan dibentuk menggunakan kombinasi utility class Tailwind yang panjang di dalam file Blade (misalnya: `class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none"`). 

Sekarang, seluruh styling telah dikonsolidasikan ke dalam arsitektur **Semantic CSS & Design Tokens**. Anda dapat mengubah warna, tipografi, ukuran tombol, border radius, hingga dimensi sidebar cukup dari satu folder CSS terpusat.

---

## 2. Struktur Direktori CSS

Semua file styling berada di dalam folder `resources/css/`:

```text
resources/
└── css/
    ├── app.css                   <-- Entry point utama yang dibaca Vite
    └── design-system/
        ├── tokens.css            <-- Variabel warna, radius, border, dan dark mode
        ├── typography.css        <-- Standar judul, sub-judul, teks angka, & font
        ├── layout.css            <-- Kerangka aplikasi (shell, header 64px, footer)
        ├── buttons.css           <-- Tombol aksi (Primary Cobalt, Secondary, dll.)
        ├── forms.css             <-- Input teks, select dropdown, date picker, label
        ├── tables.css            <-- Tabel enterprise, header 13px, baris, hover
        ├── cards.css             <-- Kartu kontainer (.erp-card), header, KPI strip
        └── navigation.css        <-- Sidebar 248px, menu link aktif, group dropdown
```

File `app.css` bertindak sebagai pintu masuk (*hub*) yang menggabungkan seluruh modul di atas secara teratur sesuai hierarki *cascade*.

---

## 3. Penjelasan Lengkap Setiap File CSS

### A. `design-system/tokens.css`
**Tanggung Jawab:** Menyimpan semua nilai desain mentah (Design Tokens) menggunakan CSS Custom Properties (`--variabel`).
- **Warna Brand Utama:**
  - `--color-deep-navy`: `#0B2545` (Warna teks utama, identitas struktural)
  - `--color-cobalt`: `#1D4E89` (Warna tombol utama / interaksi)
  - `--color-cobalt-hover`: `#153A66` (Warna saat hover tombol utama)
  - `--color-frost`: `#F8FAFC` (Warna latar belakang kanvas terang)
- **Surfaces & Borders:**
  - `--bg-app`: Warna kanvas aplikasi.
  - `--bg-sidebar`: Warna putih latar sidebar.
  - `--bg-surface`: Warna putih kartu dan tabel.
  - `--border-subtle`: `#E5E7EB` (Garis border halus enterprise).
- **Dimensi Standar:**
  - `--sidebar-width: 248px;` (Lebar sidebar desktop).
  - `--header-height: 64px;` (Tinggi topbar dan brand header).
  - `--input-height: 44px;` (Tinggi form input).
  - `--btn-height: 42px;` (Tinggi tombol aksi).
  - `--radius-sm: 4px;`, `--radius-md: 6px;`, `--radius-lg: 8px;`.
- **Neutral Dark Mode:**
  Blok `html.dark` memetakan token di atas ke palet Charcoal/Black (`#09090B`, `#111113`, `#18181B`, `#27272A`).

### B. `design-system/typography.css`
**Tanggung Jawab:** Standar hierarki teks dan format angka.
- `.page-title`: Judul utama halaman (20px di mobile, 24px di desktop, font-weight 600, tracking-tight).
- `.page-subtitle`: Keterangan pembantu di bawah judul (14px, warna muted, line-height 1.5).
- `.section-title`: Judul bagian dalam kartu atau tabel (16px, font-weight 600).
- `.section-subtitle`: Sub-judul bagian (12px, warna muted).
- `.tabular`: Utility wajib untuk angka, mata uang, tanggal, dan persentase (`font-variant-numeric: tabular-nums`).
- `.font-code`: Font monospace (JetBrains Mono) untuk kode proyek atau SKU material.

### C. `design-system/layout.css`
**Tanggung Jawab:** Kerangka luar dan kanvas layar.
- `.app-shell`: Wrapper flex layar penuh (`100vh`) yang membungkus sidebar dan konten utama.
- `.app-main-content`: Area tengah scrollable yang memuat topbar dan halaman.
- `.topbar` (atau `.app-header`): Bilah atas dengan tinggi tetap 64px (`--header-height`), sticky di bagian atas dengan garis pembatas selaras dengan sidebar.
- `.app-main`: Kontainer pembungkus konten halaman dengan padding terstandar (`p-6 lg:p-7`).
- `.app-footer`: Baris hak cipta di bagian paling bawah.

### D. `design-system/buttons.css`
**Tanggung Jawab:** Seluruh elemen interaksi tombol dan status badge sistem.
- `.btn`: Base class tombol dengan tinggi 42px, radius 6px, transisi halus, dan flex centering.
- `.btn-primary`: Tombol biru Cobalt (`#1D4E89`) untuk aksi simpan, submit, dan tambah data.
- `.btn-secondary`: Tombol putih bergaris border lembut untuk batal, filter, atau navigasi sekunder.
- `.btn-danger`: Tombol merah untuk aksi destruktif / hapus.
- `.btn-sm`: Tombol mini (tinggi 32px, font 12px) untuk aksi di dalam baris tabel.
- `.badge-success`, `.badge-warning`, `.badge-danger`, `.badge-info`, `.badge-neutral`: Status badge semantik yang terhubung langsung ke 15 status tokens di `tokens.css` (`--status-green-*`, `--status-amber-*`, `--status-red-*`, `--status-blue-*`, `--status-slate-*`), lengkap dengan mapping kontras tinggi otomatis untuk mode gelap (`html.dark`).

### E. `design-system/forms.css`
**Tanggung Jawab:** Kontrol input data dan formulir.
- `.erp-input` / `.form-input`: Input teks berukuran tinggi 44px, radius 6px, border halus, dan ring fokus biru Cobalt.
- `.erp-label` / `.form-label`: Label form berukuran 14px dengan font-weight 500 dan margin bawah 8px.
- `.erp-input-error`: Indikator border dan ring merah saat terjadi kesalahan validasi.
- Reset posisi panah select dropdown (`background-position: right 12px center`) dan indikator kalender date picker native browser.

### F. `design-system/tables.css`
**Tanggung Jawab:** Tabel data transaksi dan operasional.
- `.erp-table-container`: Pembungkus tabel dengan sudut rounded 8px, border tipis, dan overflow hidden.
- `.erp-table`: Tabel enterprise dengan border-spacing 0.
- `.erp-table th`: Header kolom 13px, font-weight 500 (medium, bukan bold), warna latar frost netral.
- `.erp-table td`: Isi sel tabel 14px, padding vertikal 12px (tinggi baris ~48-52px yang nyaman dibaca).
- `.erp-table tbody tr:hover td`: Efek sorot halus saat kursor melintas di atas baris.

### G. `design-system/cards.css`
**Tanggung Jawab:** Kartu kontainer konten dan ringkasan metrik.
- `.erp-card` / `.card`: Kontainer dasar berlatar putih (charcoal di dark mode), border halus 1px, radius 8px, dan shadow minimalis.
- `.erp-card-header`: Header kartu dengan divider pemisah tipis.
- `.erp-card-body`: Area konten kartu dengan padding 20-24px.
- `.stat-card-unified`: Grid ringkasan KPI 4 metrik terbuka tanpa kotak terpisah.

### H. `design-system/navigation.css`
**Tanggung Jawab:** Navigasi sidebar kiri.
- `.sidebar`: Kontainer sidebar desktop lebar 248px (`--sidebar-width`).
- `.sidebar-header`: Header brand BuildSync ERP tinggi 64px.
- `.sidebar-nav`: Area daftar menu dengan custom scrollbar tersembunyi.
- `.sidebar-item`: Tautan navigasi standar (14px, font-weight normal, hover state lembut).
- `.sidebar-item-active`: Indikator menu yang sedang aktif (tint biru Cobalt lembut dengan font-weight medium).
- `.sidebar-group-btn`: Tombol accordion kategori menu yang dapat dibuka/tutup.
- `.sidebar-user-footer`: Informasi profil pengguna dan tombol logout di bagian bawah sidebar.

---

## 4. Panduan Praktis Kustomisasi Desain Visual

Sebagai UI/UX Designer, berikut cara melakukan penyesuaian visual tanpa perlu menyentuh logika PHP:

### Cara 1: Mengubah Warna Global
Buka file `resources/css/design-system/tokens.css`:
```css
/* Ganti warna utama brand: */
--color-cobalt: #1D4E89;       /* Ganti dengan kode HEX baru */
--color-cobalt-hover: #153A66; /* Ganti dengan versi sedikit lebih gelap */

/* Ganti warna teks utama: */
--text-main: #0B2545;
```

### Cara 2: Mengubah Dimensi Komponen
Buka file `resources/css/design-system/tokens.css`:
```css
--sidebar-width: 260px;  /* Ingin sidebar lebih lebar? Ubah dari 248px ke 260px */
--header-height: 64px;   /* Mengatur tinggi topbar dan brand header */
--input-height:  40px;   /* Ingin input lebih pendek? Ubah dari 44px ke 40px */
--btn-height:    40px;   /* Menyesuaikan tinggi tombol */
--radius-md:     8px;    /* Ingin sudut tombol & input lebih melengkung? Ubah di sini */
```

### Cara 3: Mengubah Tipografi & Ukuran Teks
Buka file `resources/css/design-system/typography.css`:
```css
/* Mengubah ukuran judul halaman */
.page-title {
    font-size: 1.5rem;    /* Ubah ukuran font judul */
    font-weight: 700;     /* Ubah ke bold jika diperlukan */
}

/* Mengubah warna atau ukuran sub-judul */
.page-subtitle {
    font-size: 0.875rem;
    color: var(--text-muted);
}
```

### Cara 4: Mengubah Gaya Tampilan Tabel
Buka file `resources/css/design-system/tables.css`:
```css
/* Mengatur kerapatan (density) baris tabel */
.erp-table td {
    padding: 0.5rem 1rem; /* Ubah dari 0.75rem ke 0.5rem untuk tabel lebih rapat */
}
```

---

## 5. Contoh Praktis: Menggunakan Semantic Classes di Blade

Berikut perbandingan penulisan sebelum dan sesudah refactoring.

### Contoh A: Kartu Formulir / Konten
**Sebelum (Repetitif & Sukar Dimodifikasi):**
```html
<div class="max-w-4xl rounded-lg p-5 sm:p-6 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
    <!-- Konten Form -->
</div>
```

**Sesudah (Bersih, Semantik, & Terpusat):**
```html
<div class="erp-card max-w-4xl p-5 sm:p-6">
    <!-- Konten Form -->
</div>
```

---

### Contoh B: Header Halaman
**Sebelum:**
```html
<h2 class="text-xl lg:text-2xl font-semibold tracking-tight text-[#0B2545] dark:text-[#F4F4F5]">Daftar Proyek</h2>
<p class="mt-1 text-sm text-[#64748B] dark:text-[#A1A1AA] leading-relaxed">Kelola seluruh proyek konstruksi aktif.</p>
```

**Sesudah:**
```html
<h2 class="page-title">Daftar Proyek</h2>
<p class="page-subtitle">Kelola seluruh proyek konstruksi aktif.</p>
```

---

### Contoh C: Item Navigasi Sidebar
**Sebelum:**
```html
<a href="{{ route('dashboard') }}"
   class="flex items-center gap-3 text-sm rounded-md transition-colors px-3 py-2 {{ $isActive ? 'bg-[#1D4E89]/10 text-[#1D4E89] dark:bg-[#27272A] dark:text-[#F4F4F5] font-medium' : 'text-[#0B2545]/80 hover:text-[#0B2545] hover:bg-slate-50 dark:text-[#A1A1AA] dark:hover:text-[#F4F4F5] dark:hover:bg-white/5 font-normal' }}">
    <span>Dashboard</span>
</a>
```

**Sesudah:**
```html
<a href="{{ route('dashboard') }}"
   class="sidebar-item {{ $isActive ? 'sidebar-item-active' : '' }}">
    <span>Dashboard</span>
</a>
```

---

## 6. Cara Menambahkan Komponen Baru

Jika Anda mendesain komponen baru (misalnya `.stat-badge` atau `.notification-pill`):

1. **Tentukan Modul yang Tepat:**
   - Elemen tombol/aksi $\rightarrow$ `design-system/buttons.css`
   - Elemen form $\rightarrow$ `design-system/forms.css`
   - Kontainer informasi $\rightarrow$ `design-system/cards.css`
   - Elemen teks $\rightarrow$ `design-system/typography.css`
2. **Gunakan Token yang Sudah Ada:** Selalu gunakan `var(--color-cobalt)`, `var(--border-subtle)`, dsb., agar konsisten secara otomatis.
3. **Sertakan Dukungan Dark Mode:**
   ```css
   .status-pill {
       background-color: var(--color-frost);
       color: var(--text-main);
       border: 1px solid var(--border-subtle);
   }
   html.dark .status-pill {
       background-color: var(--dark-surface);
       color: var(--dark-text-primary);
       border-color: var(--dark-border);
   }
   ```

---

## 7. Cara Mempratinjau Perubahan di Browser

Setelah melakukan perubahan pada file CSS atau Blade:

1. **Jika Menjalankan Development Server (Hot Reload):**
   ```bash
   npm run dev
   ```
   Perubahan pada CSS dan Blade akan langsung ter-update secara instan di browser.

2. **Membersihkan Cache Tampilan Blade:**
   Jika Anda mengubah file Blade dan ingin memastikan cache tampilan bersih:
   ```bash
   php artisan view:clear
   ```

3. **Membangun Asset untuk Produksi:**
   ```bash
   npm run build
   # atau
   node_modules/.bin/vite build
   ```

4. **Menjalankan Pengujian Otomatis:**
   Pastikan tidak ada tes yang rusak akibat perubahan:
   ```bash
   php artisan test
   ```
