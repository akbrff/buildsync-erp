<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="project_id" class="erp-label">Proyek Terkait <span class="text-red-500">*</span></label>
        @if ($schedule)
            <div class="rounded-md px-3 py-2 text-xs font-medium text-slate-800"
                 style="background:#f8fafc; border:1px solid #e2e8f0;">
                <span class="font-code font-bold text-slate-700">{{ $schedule->project->project_code }}</span>
                — {{ $schedule->project->name }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Proyek tidak dapat dialihkan setelah jadwal pekerjaan dibuat.</p>
        @else
            <select id="project_id" name="project_id" class="erp-input @error('project_id') erp-input-error @enderror">
                <option value="">— Pilih Proyek Konstruksi —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected((string) old('project_id', $selectedProjectId ?? '') === (string) $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        @endif
    </div>

    <div class="md:col-span-2">
        <label for="name" class="erp-label">Nama Tahapan Pekerjaan <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $schedule?->name) }}"
               placeholder="Contoh: Pekerjaan Pondasi & Struktur Bawah"
               class="erp-input @error('name') erp-input-error @enderror">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="erp-label">Tanggal Mulai <span class="text-red-500">*</span></label>
        <input id="start_date" name="start_date" type="date"
               value="{{ old('start_date', $schedule?->start_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('start_date') erp-input-error @enderror">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="erp-label">Target Selesai <span class="text-red-500">*</span></label>
        <input id="end_date" name="end_date" type="date"
               value="{{ old('end_date', $schedule?->end_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('end_date') erp-input-error @enderror">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="weight" class="erp-label">Bobot Pekerjaan (%) <span class="text-red-500">*</span></label>
        <input id="weight" name="weight" type="number" min="0.01" max="100" step="0.01"
               value="{{ old('weight', $schedule?->weight) }}"
               placeholder="Contoh: 15.5"
               class="erp-input tabular @error('weight') erp-input-error @enderror">
        <p class="mt-1 text-[11px] text-slate-400">Total kumulatif bobot seluruh tahapan dalam satu proyek tidak boleh melampaui 100%.</p>
        @error('weight')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="erp-label">Deskripsi Rincian Pekerjaan</label>
        <textarea id="description" name="description" rows="3"
                  placeholder="Keterangan spesifikasi teknis, metode kerja, atau deliverable tahapan ini."
                  class="w-full rounded-lg border border-slate-300 p-2.5 text-xs text-[#2C3E50] transition focus:border-[#136F9A] focus:outline-none focus:ring-1 focus:ring-[#136F9A] @error('description') erp-input-error @enderror">{{ old('description', $schedule?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
