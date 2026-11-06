---
name: anti-slop-erp
description: Strict guidelines for high-density, professional ERP dashboard design avoiding AI slop aesthetics.
---

# Anti-AI-Slop & Enterprise Design Rules

## Negative Constraints (Yang Dilarang)
- **No generic AI palettes:** Dilarang menggunakan aksen gradien ungu/pink/indigo neon atau "cosmic dark mode".
- **No excessive decoration:** Hindari drop-shadow tebal, border-radius balon (>8px pada input/tabel/kartu dilarang), dan efek *frosted glass* / blur.
- **No meaningless whitespace:** ERP butuh densitas data tinggi. Jangan buat margin raksasa yang memaksa user scroll tanpa alasan.

## Positive Constraints (Yang Harus Diterapkan)
- **High Information Density:** Gunakan layout tabular, data-grid terstruktur, dan compact paddings (misal Tailwind: `p-2` s/d `p-4`, teks `text-xs` atau `text-sm`).
- **Typography:** Gunakan `font-sans` sistem yang bersih (`Inter`, `system-ui`) dengan tabular numbers (`tabular-nums` atau `font-mono`) untuk angka, mata uang, dan metrik.
- **Color Token Fungsional:**
  - Background: Neutral/slate terang atau gelap terstruktur (1–2 shade kontras).
  - Borders: `1px solid` presisi untuk memisahkan section (misal: `border-slate-200` atau `border-zinc-800`).
  - Status color: Hanya gunakan warna solid untuk semantik (Hijau = Aktif/Selesai, Kuning = Pending, Merah = Delay/Overbudget, Biru = Aksi/Primary).
