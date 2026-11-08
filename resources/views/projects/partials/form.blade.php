<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="project_code" class="erp-label">Kode Proyek <span class="text-red-500">*</span></label>
        <input id="project_code" name="project_code" type="text"
               value="{{ old('project_code', $project?->project_code) }}"
               placeholder="Contoh: PRJ-001"
               class="erp-input @error('project_code') erp-input-error @enderror">
        @error('project_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="name" class="erp-label">Nama Proyek <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $project?->name) }}"
               placeholder="Nama proyek konstruksi"
               class="erp-input @error('name') erp-input-error @enderror">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location" class="erp-label">Lokasi</label>
        <input id="location" name="location" type="text"
               value="{{ old('location', $project?->location) }}"
               placeholder="Kota / Alamat lokasi pekerjaan"
               class="erp-input @error('location') erp-input-error @enderror">
        @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="erp-label">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" class="erp-input @error('status') erp-input-error @enderror">
            @foreach (['planning' => 'Perencanaan', 'ongoing' => 'Berjalan', 'completed' => 'Selesai', 'on_hold' => 'Ditunda'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status ?? 'planning') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="erp-label">Tanggal Mulai <span class="text-red-500">*</span></label>
        <input id="start_date" name="start_date" type="date"
               value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('start_date') erp-input-error @enderror">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="erp-label">Tanggal Selesai (Target)</label>
        <input id="end_date" name="end_date" type="date"
               value="{{ old('end_date', $project?->end_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('end_date') erp-input-error @enderror">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="budget" class="erp-label">Pagu Anggaran / Nilai Kontrak (Rp) <span class="text-red-500">*</span></label>
        <x-erp-currency-input name="budget" id="budget" :value="old('budget', $project?->budget)" required placeholder="Contoh: 500.000.000" />
        @error('budget')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="erp-label">Deskripsi & Catatan Proyek</label>
        <textarea id="description" name="description" rows="3"
                  placeholder="Keterangan lingkup pekerjaan, penanggung jawab, spesifikasi khusus, dll."
                  class="w-full rounded-md border border-slate-300 p-2.5 text-xs text-slate-800 transition focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 @error('description') erp-input-error @enderror">{{ old('description', $project?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
