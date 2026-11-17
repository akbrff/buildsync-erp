<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    {{-- Kode NIK --}}
    <div>
        <label for="worker_code" class="erp-label mb-1 block">
            {{ __('workforce.code_label') }} <span class="text-red-500">*</span>
        </label>
        <input id="worker_code" name="worker_code" type="text"
               value="{{ old('worker_code', $worker?->worker_code) }}"
               required
               class="erp-input font-code uppercase"
               placeholder="{{ __('workforce.code_placeholder') }}">
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.code_hint') }}</p>
        @error('worker_code')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama Lengkap --}}
    <div>
        <label for="name" class="erp-label mb-1 block">
            {{ __('workforce.name_label') }} <span class="text-red-500">*</span>
        </label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $worker?->name) }}"
               required
               class="erp-input font-medium"
               placeholder="{{ __('workforce.name_placeholder') }}">
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.name_hint') }}</p>
        @error('name')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Jabatan / Spesialisasi --}}
    <div>
        <label for="position" class="erp-label mb-1 block">
            {{ __('workforce.position_label') }} <span class="text-red-500">*</span>
        </label>
        <input id="position" name="position" type="text" list="positions-list"
               value="{{ old('position', $worker?->position) }}"
               required
               class="erp-input"
               placeholder="{{ __('workforce.position_placeholder') }}">
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
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.position_hint') }}</p>
        @error('position')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kontak Telepon --}}
    <div>
        <label for="phone" class="erp-label mb-1 block">
            {{ __('workforce.phone_label') }}
        </label>
        <input id="phone" name="phone" type="text"
               inputmode="numeric"
               maxlength="16"
               autocomplete="tel"
               value="{{ old('phone', $worker?->formatted_phone ?? $worker?->phone) }}"
               class="erp-input tabular font-medium @error('phone') erp-input-error @enderror"
               placeholder="{{ __('workforce.phone_placeholder') }}">
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.phone_hint') }}</p>
        @error('phone')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tarif Upah Harian --}}
    <div>
        <label for="daily_rate" class="erp-label mb-1 block">
            {{ __('workforce.daily_rate_label') }} <span class="text-red-500">*</span>
        </label>
        <x-erp-currency-input name="daily_rate" id="daily_rate" :value="old('daily_rate', $worker?->daily_rate ? (int)$worker->daily_rate : '')" required placeholder="150.000" />
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.daily_rate_hint') }}</p>
        @error('daily_rate')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status Ketersediaan --}}
    <div>
        <label for="status" class="erp-label mb-1 block">
            {{ __('workforce.status_label') }} <span class="text-red-500">*</span>
        </label>
        <select id="status" name="status" class="erp-input font-medium" required>
            <option value="active" @selected(old('status', $worker?->status ?? 'active') === 'active')>
                {{ __('workforce.status_active_option') }}
            </option>
            <option value="inactive" @selected(old('status', $worker?->status) === 'inactive')>
                {{ __('workforce.status_inactive_option') }}
            </option>
        </select>
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.status_hint') }}</p>
        @error('status')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Catatan & Sertifikasi --}}
    <div class="sm:col-span-2">
        <label for="notes" class="erp-label mb-1 block">
            {{ __('workforce.notes_label') }}
        </label>
        <textarea id="notes" name="notes" rows="3"
                  class="w-full rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-2.5 text-xs text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-1 focus:ring-[#1D4E89] @error('notes') erp-input-error @enderror"
                  placeholder="{{ __('workforce.notes_placeholder') }}">{{ old('notes', $worker?->notes) }}</textarea>
        <p class="mt-1 text-[11px] text-slate-400">{{ __('workforce.notes_hint') }}</p>
        @error('notes')
            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

@once
<script>
(function() {
    function formatPhoneNumber(val) {
        if (!val) return '';
        // Hapus semua karakter non-angka
        let digits = String(val).replace(/\D/g, '');

        // Jika nomor dipaste dengan format internasional +62 atau 62 (panjang >= 10), normalisasi ke 0
        if (digits.startsWith('62') && digits.length >= 10) {
            digits = '0' + digits.slice(2);
        }

        // Batasi maksimal 13 digit angka
        digits = digits.slice(0, 13);

        if (digits.length <= 4) {
            return digits;
        } else if (digits.length <= 8) {
            return digits.slice(0, 4) + '-' + digits.slice(4);
        } else {
            return digits.slice(0, 4) + '-' + digits.slice(4, 8) + '-' + digits.slice(8);
        }
    }

    function handlePhoneInput(input) {
        const originalValue = input.value;
        const cursorPosition = input.selectionStart || originalValue.length;

        // Hitung berapa digit angka murni sebelum kursor
        const digitsBeforeCursor = originalValue.slice(0, cursorPosition).replace(/\D/g, '').length;

        const formatted = formatPhoneNumber(originalValue);
        input.value = formatted;

        // Hitung ulang posisi kursor agar tidak melompat ke akhir input
        let newPos = 0;
        let countedDigits = 0;
        for (let i = 0; i < formatted.length; i++) {
            if (/\d/.test(formatted[i])) {
                countedDigits++;
            }
            if (countedDigits === digitsBeforeCursor) {
                newPos = i + 1;
                break;
            }
        }
        if (digitsBeforeCursor === 0) newPos = 0;
        if (digitsBeforeCursor >= formatted.replace(/\D/g, '').length) newPos = formatted.length;

        input.setSelectionRange(newPos, newPos);
    }

    function initPhoneMask() {
        const phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

        // Pastikan saat load (edit page) nomor telepon langsung terformat
        if (phoneInput.value) {
            phoneInput.value = formatPhoneNumber(phoneInput.value);
        }

        phoneInput.addEventListener('input', function() {
            handlePhoneInput(this);
        });

        phoneInput.addEventListener('keydown', function(e) {
            // Ketika menekan backspace tepat setelah tanda hubung '-', hapus juga digit sebelumnya
            if (e.key === 'Backspace' && this.selectionStart === this.selectionEnd) {
                const pos = this.selectionStart;
                if (pos > 0 && this.value[pos - 1] === '-') {
                    e.preventDefault();
                    const val = this.value;
                    this.value = val.slice(0, pos - 2) + val.slice(pos);
                    handlePhoneInput(this);
                }
            }
        });

        phoneInput.addEventListener('paste', function() {
            setTimeout(() => {
                handlePhoneInput(this);
            }, 0);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneMask);
    } else {
        initPhoneMask();
    }
})();
</script>
@endonce
