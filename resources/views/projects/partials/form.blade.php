<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="project_code" class="block text-sm font-medium text-slate-700">Kode Proyek</label>
        <input id="project_code" name="project_code" type="text" value="{{ old('project_code', $project?->project_code) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('project_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Nama Proyek</label>
        <input id="name" name="name" type="text" value="{{ old('name', $project?->name) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-slate-700">Lokasi</label>
        <input id="location" name="location" type="text" value="{{ old('location', $project?->location) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach (['planning' => 'Perencanaan', 'ongoing' => 'Berjalan', 'completed' => 'Selesai', 'on_hold' => 'Ditunda'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status ?? 'planning') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-slate-700">Tanggal Mulai</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="block text-sm font-medium text-slate-700">Tanggal Selesai</label>
        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $project?->end_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="budget" class="block text-sm font-medium text-slate-700">Anggaran</label>
        <input id="budget" name="budget" type="number" min="0" step="0.01" value="{{ old('budget', $project?->budget) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('budget')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $project?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
