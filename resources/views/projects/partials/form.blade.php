<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="project_code" class="erp-label">{{ __('projects.project_code') }} <span class="text-red-500">*</span></label>
        <input id="project_code" name="project_code" type="text"
               value="{{ old('project_code', $project?->project_code) }}"
               placeholder="Contoh: PRJ-001"
               class="erp-input @error('project_code') erp-input-error @enderror">
        @error('project_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="name" class="erp-label">{{ __('projects.name') }} <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $project?->name) }}"
               placeholder="Nama proyek konstruksi"
               class="erp-input @error('name') erp-input-error @enderror">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location" class="erp-label">{{ __('projects.location') }}</label>
        <input id="location" name="location" type="text"
               value="{{ old('location', $project?->location) }}"
               placeholder="Kota / Alamat lokasi pekerjaan"
               class="erp-input @error('location') erp-input-error @enderror">
        @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="erp-label">{{ __('common.status') }} <span class="text-red-500">*</span></label>
        <select id="status" name="status" class="erp-input @error('status') erp-input-error @enderror">
            @foreach (['planning' => __('projects.status_planning'), 'ongoing' => __('projects.status_ongoing'), 'completed' => __('projects.status_completed'), 'on_hold' => __('projects.status_on_hold')] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status ?? 'planning') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="erp-label">{{ __('projects.start_date') }} <span class="text-red-500">*</span></label>
        <input id="start_date" name="start_date" type="date"
               value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('start_date') erp-input-error @enderror">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="erp-label">{{ __('projects.target_end_date') }}</label>
        <input id="end_date" name="end_date" type="date"
               value="{{ old('end_date', $project?->end_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('end_date') erp-input-error @enderror">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="budget" class="erp-label">{{ __('projects.contract_value') }} (Rp) <span class="text-red-500">*</span></label>
        <x-erp-currency-input name="budget" id="budget" :value="old('budget', $project?->budget)" required placeholder="500.000.000" />
        @error('budget')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="erp-label">{{ __('projects.description') }}</label>
        <textarea id="description" name="description" rows="3"
                  placeholder="Keterangan lingkup pekerjaan, penanggung jawab, spesifikasi khusus, dll."
                  class="w-full rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-3 text-sm text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-1 focus:ring-[#1D4E89] @error('description') erp-input-error @enderror">{{ old('description', $project?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
