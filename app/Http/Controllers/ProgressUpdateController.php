<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgressUpdateRequest;
use App\Models\ProjectSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgressUpdateController extends Controller
{
    public function store(StoreProgressUpdateRequest $request, ProjectSchedule $schedule)
    {
        $data = $request->validated();
        $data['recorded_at'] = Carbon::parse($data['recorded_at'])->toDateString();

        DB::transaction(function () use ($data, $schedule) {
            ProjectSchedule::whereKey($schedule->id)->lockForUpdate()->firstOrFail();

            $latest = $schedule->progressUpdates()
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->first();

            if ($latest) {
                $errors = [];

                if ($data['recorded_at'] < $latest->recorded_at->toDateString()) {
                    $errors['recorded_at'] = 'Tanggal pencatatan tidak boleh lebih awal dari catatan terakhir ('
                        .$latest->recorded_at->format('d M Y').').';
                }

                $isLower = (int) round((float) $data['progress_percentage'] * 100)
                    < (int) round((float) $latest->progress_percentage * 100);

                if ($isLower && ! $data['is_correction']) {
                    $errors['is_correction'] = 'Progres lebih rendah dari catatan terakhir ('
                        .number_format((float) $latest->progress_percentage, 2, ',', '.')
                        .'%). Centang "koreksi" untuk melanjutkan.';
                }

                if ($isLower && blank($data['notes'] ?? null)) {
                    $errors['notes'] = 'Catatan wajib diisi untuk koreksi progres.';
                }

                if ($errors) {
                    throw ValidationException::withMessages($errors);
                }
            }

            $schedule->progressUpdates()->create($data);
        });

        return redirect()
            ->route('schedules.show', $schedule)
            ->with('success', __('messages.progress_recorded'));
    }
}