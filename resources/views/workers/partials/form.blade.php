<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="worker_code" class="block text-sm font-medium text-slate-700">Kode Tenaga Kerja</label>
        <input id="worker_code" name="worker_code" type="text" value="{{ old('worker_code', $worker?->worker_code) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('worker_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
        <input id="name" name="name" type="text" value="{{ old('name', $worker?->name) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="position" class="block text-sm font-medium text-slate-700">Jabatan</label>
        <input id="position" name="position" type="text" value="{{ old('position', $worker?->position) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $worker?->phone) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="daily_rate" class="block text-sm font-medium text-slate-700">Upah per Hari</label>
        <input id="daily_rate" name="daily_rate" type="number" min="0" step="0.01" value="{{ old('daily_rate', $worker?->daily_rate) }}" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('daily_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach (['active' => 'Aktif', 'inactive' => 'Tidak Aktif'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $worker?->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="block text-sm font-medium text-slate-700">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $worker?->notes) }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>