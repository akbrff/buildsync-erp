<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="project_id" class="block text-sm font-medium text-slate-700">Proyek</label>
        @if ($schedule)
            <p class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                {{ $schedule->project->project_code }} - {{ $schedule->project->name }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Proyek tidak dapat diubah setelah jadwal dibuat.</p>
        @else
            <select id="project_id" name="project_id" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Pilih proyek --</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected((string) old('project_id', $selectedProjectId ?? '') === (string) $project->id)>{{ $project->project_code }} - {{ $project->name }}</option>
                @endforeach
            </select>
            @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        @endif
    </div>

    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-slate-700">Nama Pekerjaan</label>
        <input id="name" name="name" type="text" value="{{ old('name', $schedule?->name) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-slate-700">Tanggal Mulai</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $schedule?->start_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="block text-sm font-medium text-slate-700">Target Selesai</label>
        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $schedule?->end_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="weight" class="block text-sm font-medium text-slate-700">Bobot Pekerjaan (%)</label>
        <input id="weight" name="weight" type="number" min="0.01" max="100" step="0.01" value="{{ old('weight', $schedule?->weight) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('weight')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $schedule?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>