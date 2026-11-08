<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    {{-- Kode NIK --}}
    <div>
        <label for="worker_code" class="erp-label mb-1 block">
            Kode Tenaga Kerja / NIK <span class="text-red-500">*</span>
        </label>
        <input id="worker_code" name="worker_code" type="text"
               value="{{ old('worker_code', $worker?->worker_code) }}"
               required
               class="erp-input font-code uppercase"
               placeholder="Contoh: WRK-2026-001">
        <p class="mt-1 text-[11px] text-slate-400">Kode unik identifikasi tenaga kerja di proyek.</p>
        @error('worker_code')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama Lengkap --}}
    <div>
        <label for="name" class="erp-label mb-1 block">
            Nama Lengkap <span class="text-red-500">*</span>
        </label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $worker?->name) }}"
               required
               class="erp-input font-medium"
               placeholder="Contoh: Bambang Sutrisno">
        <p class="mt-1 text-[11px] text-slate-400">Nama lengkap sesuai identitas resmi.</p>
        @error('name')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Jabatan / Spesialisasi --}}
    <div>
        <label for="position" class="erp-label mb-1 block">
            Jabatan / Spesialisasi <span class="text-red-500">*</span>
        </label>
        <input id="position" name="position" type="text" list="positions-list"
               value="{{ old('position', $worker?->position) }}"
               required
               class="erp-input"
               placeholder="Pilih atau ketik jabatan...">
        <datalist id="positions-list">
            <option value="Mandor Struktur">
            <option value="Mandor Finishing">
            <option value="Tukang Besi / Pembesian">
            <option value="Tukang Batu & Plester">
            <option value="Tukang Kayu / Bekisting">
            <option value="Tukang Cat & Finishing">
            <option value="Tukang Listrik (MEP)">
            <option value="Tukang Plumbing / Pipa">
            <option value="Operator Alat Berat">
            <option value="Helper / Kenek Proyek">
        </datalist>
        <p class="mt-1 text-[11px] text-slate-400">Spesialisasi keahlian utama di lapangan.</p>
        @error('position')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kontak Telepon --}}
    <div>
        <label for="phone" class="erp-label mb-1 block">
            Nomor Telepon / WhatsApp
        </label>
        <input id="phone" name="phone" type="text"
               value="{{ old('phone', $worker?->phone) }}"
               class="erp-input tabular"
               placeholder="Contoh: 0812-3456-7890">
        <p class="mt-1 text-[11px] text-slate-400">Nomor aktif untuk koordinasi penugasan.</p>
        @error('phone')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tarif Upah Harian --}}
    <div>
        <label for="daily_rate" class="erp-label mb-1 block">
            Tarif Upah Harian (Rp) <span class="text-red-500">*</span>
        </label>
        <x-erp-currency-input name="daily_rate" id="daily_rate" :value="old('daily_rate', $worker?->daily_rate ? (int)$worker->daily_rate : '')" required placeholder="Contoh: 150.000" />
        <p class="mt-1 text-[11px] text-slate-400">Standar upah kerja 1 shift / 8 jam per hari.</p>
        @error('daily_rate')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status Ketersediaan --}}
    <div>
        <label for="status" class="erp-label mb-1 block">
            Status Keaktifan <span class="text-red-500">*</span>
        </label>
        <select id="status" name="status" class="erp-input font-medium" required>
            <option value="active" @selected(old('status', $worker?->status ?? 'active') === 'active')>
                Aktif (Siap Kerja / Berjalan)
            </option>
            <option value="inactive" @selected(old('status', $worker?->status) === 'inactive')>
                Tidak Aktif (Standby / Istirahat)
            </option>
        </select>
        <p class="mt-1 text-[11px] text-slate-400">Status menentukan keikutsertaan dalam hitungan beban payroll harian.</p>
        @error('status')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Catatan & Sertifikasi --}}
    <div class="sm:col-span-2">
        <label for="notes" class="erp-label mb-1 block">
            Catatan Kualifikasi, Sertifikasi K3 & Pengalaman
        </label>
        <textarea id="notes" name="notes" rows="3"
                  class="erp-input"
                  placeholder="Contoh: Memiliki Sertifikat K3 Ketinggian BNSP, pengalaman 5 tahun di konstruksi high-rise...">{{ old('notes', $worker?->notes) }}</textarea>
        <p class="mt-1 text-[11px] text-slate-400">Informasi tambahan mengenai lisensi, keahlian khusus, atau riwayat kerja.</p>
        @error('notes')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
