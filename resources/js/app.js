import './bootstrap';

/**
 * BuildSync ERP — Global Currency Formatter Utility
 * Otomatis memformat angka input menjadi format Rupiah dengan pemisah ribuan titik.
 */
(function initErpCurrency() {
    function formatRupiah(val) {
        if (!val && val !== 0) return '';
        const digits = String(val).replace(/\D/g, '');
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(val) {
        if (!val && val !== 0) return '';
        return String(val).replace(/\D/g, '');
    }

    function handleCurrencyInput(input) {
        const rawId = input.dataset.currencyTarget;
        const rawInput = rawId ? document.getElementById(rawId) : null;

        const originalValue = input.value;
        const cursorPosition = input.selectionStart || originalValue.length;

        const digitsBeforeCursor = originalValue.slice(0, cursorPosition).replace(/\D/g, '').length;
        const cleanDigits = unformatRupiah(originalValue);
        const formatted = formatRupiah(cleanDigits);

        input.value = formatted;
        if (rawInput) {
            rawInput.value = cleanDigits;
        }

        // Hitung ulang posisi kursor agar tetap presisi
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

    // Event listener delegasi untuk seluruh input currency
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('erp-currency-display')) {
            handleCurrencyInput(e.target);
        }
    });

    // Sanitasi sebelum submit form agar request payload selalu berupa integer/numeric murni
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.querySelectorAll) return;
        form.querySelectorAll('.erp-currency-display').forEach(function(display) {
            const rawId = display.dataset.currencyTarget;
            const rawInput = rawId ? document.getElementById(rawId) : null;
            if (rawInput) {
                rawInput.value = unformatRupiah(display.value);
            }
        });
    });

    window.formatRupiah = formatRupiah;
    window.unformatRupiah = unformatRupiah;
})();
