<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="project_id" class="erp-label">{{ __('projects.title') }} <span class="text-red-500">*</span></label>
        @if ($schedule)
            <div class="rounded-md px-3.5 py-2.5 text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] bg-[#F4F8FB] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                <span class="text-[13px] font-medium text-[#64748B] dark:text-slate-300">{{ $schedule->project->project_code }}</span>
                — {{ $schedule->project->name }}
            </div>
            <p class="mt-1 text-xs text-[#64748B] dark:text-slate-400">{{ __('schedules.project_locked_note') }}</p>
        @else
            <select id="project_id" name="project_id" class="erp-input @error('project_id') erp-input-error @enderror">
                <option value="">{{ __('schedules.select_project') }}</option>
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
        <label for="name" class="erp-label">{{ __('schedules.stage_name') }} <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $schedule?->name) }}"
               placeholder="{{ __('schedules.stage_name_placeholder') }}"
               class="erp-input @error('name') erp-input-error @enderror">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="start_date" class="erp-label">{{ __('projects.start_date') }} <span class="text-red-500">*</span></label>
        <input id="start_date" name="start_date" type="date"
               value="{{ old('start_date', $schedule?->start_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('start_date') erp-input-error @enderror">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_date" class="erp-label">{{ __('projects.target_end_date') }} <span class="text-red-500">*</span></label>
        <input id="end_date" name="end_date" type="date"
               value="{{ old('end_date', $schedule?->end_date?->format('Y-m-d')) }}"
               class="erp-input tabular @error('end_date') erp-input-error @enderror">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="weight" class="erp-label">{{ __('schedules.weight') }} (%) <span class="text-red-500">*</span></label>
        <input id="weight" name="weight" type="number" min="0.01" max="100" step="0.01"
               value="{{ old('weight', $schedule?->weight) }}"
               placeholder="Contoh: 15.5"
               class="erp-input tabular @error('weight') erp-input-error @enderror">
        <p class="mt-1 text-[11px] text-slate-400">{{ __('schedules.weight_note') }}</p>
        @error('weight')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="erp-label">{{ __('schedules.work_details_desc') }}</label>
        <textarea id="description" name="description" rows="3"
                  placeholder="{{ __('schedules.work_details_desc_placeholder') }}"
                  class="w-full rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-3 text-sm text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-2 focus:ring-[#1D4E89]/20 @error('description') erp-input-error @enderror">{{ old('description', $schedule?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
