@props([
    'name',
    'id' => null,
    'value' => null,
    'placeholder' => '0',
    'required' => false,
    'prefix' => 'Rp',
    'class' => '',
])

@php
    $id = $id ?? $name;
    $initial = old($name, $value ?? '');
    if (is_numeric($initial) && $initial !== '') {
        $num = (float) $initial;
        $rawValue = floor($num) == $num ? (int)$num : $num;
        $displayValue = number_format($rawValue, 0, ',', '.');
    } elseif ($initial !== '' && $initial !== null) {
        $digits = preg_replace('/\D/', '', (string) $initial);
        $rawValue = $digits !== '' ? (int) $digits : '';
        $displayValue = $rawValue !== '' ? number_format($rawValue, 0, ',', '.') : '';
    } else {
        $rawValue = '';
        $displayValue = '';
    }
    $hasError = $errors->has($name);
@endphp

<div class="relative">
    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-semibold text-slate-500">
        {{ $prefix }}
    </span>
    <input
        type="text"
        id="{{ $id }}_display"
        value="{{ $displayValue }}"
        placeholder="{{ $placeholder }}"
        class="erp-input pl-9 tabular font-semibold erp-currency-display {{ $hasError ? 'erp-input-error' : '' }} {{ $class }}"
        data-currency-target="{{ $id }}"
        autocomplete="off"
        inputmode="numeric"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['name', 'value', 'id', 'class', 'required', 'placeholder']) }}
    />
    <input
        type="hidden"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $rawValue }}"
        class="erp-currency-raw"
    />
</div>

@once
<script>
(function() {
    function formatRupiahString(val) {
        if (!val) return '';
        const digits = String(val).replace(/\D/g, '');
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function handleCurrencyInput(input) {
        const rawId = input.dataset.currencyTarget;
        const rawInput = rawId ? document.getElementById(rawId) : null;

        const originalValue = input.value;
        const cursorPosition = input.selectionStart || originalValue.length;

        // Hitung berapa digit angka murni sebelum kursor
        const digitsBeforeCursor = originalValue.slice(0, cursorPosition).replace(/\D/g, '').length;

        const cleanDigits = originalValue.replace(/\D/g, '');
        const formatted = formatRupiahString(cleanDigits);

        input.value = formatted;
        if (rawInput) {
            rawInput.value = cleanDigits;
        }

        // Hitung ulang posisi kursor agar tidak melompat
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
        if (digitsBeforeCursor >= cleanDigits.length) newPos = formatted.length;

        input.setSelectionRange(newPos, newPos);
    }

    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('erp-currency-display')) {
            handleCurrencyInput(e.target);
        }
    });

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.querySelectorAll) return;
        form.querySelectorAll('.erp-currency-display').forEach(function(display) {
            const rawId = display.dataset.currencyTarget;
            const rawInput = rawId ? document.getElementById(rawId) : null;
            if (rawInput) {
                rawInput.value = display.value.replace(/\D/g, '');
            }
        });
    });
})();
</script>
@endonce
