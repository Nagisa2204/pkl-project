import Chart from 'chart.js/auto';

window.Chart = Chart;

window.createSearchableSelect = ({ value, options, multiple = false, placeholder = 'Pilih opsi' }) => ({
    open: false,
    query: '',
    value,
    options,
    multiple,
    placeholder,

    get filteredOptions() {
        const needle = this.query.trim().toLocaleLowerCase('id-ID');

        if (! needle) {
            return this.options;
        }

        return this.options.filter((option) => option.label.toLocaleLowerCase('id-ID').includes(needle));
    },

    get selectedValues() {
        if (this.multiple) {
            return Array.isArray(this.value) ? this.value.map(String) : [];
        }

        return this.value === null || this.value === undefined || this.value === '' ? [] : [String(this.value)];
    },

    get label() {
        const labels = this.options
            .filter((option) => this.selectedValues.includes(String(option.value)))
            .map((option) => option.label);

        return labels.length ? labels.join(', ') : this.placeholder;
    },

    isSelected(optionValue) {
        return this.selectedValues.includes(String(optionValue));
    },

    select(optionValue) {
        const normalized = String(optionValue);

        if (this.multiple) {
            const values = this.selectedValues;
            this.value = values.includes(normalized)
                ? values.filter((item) => item !== normalized)
                : [...values, normalized];
            return;
        }

        this.value = normalized;
        this.open = false;
        this.query = '';
    },

    clear() {
        this.value = this.multiple ? [] : '';
        this.query = '';
    },
});

window.openMidtransSnap = (token, redirectUrl) => {
    if (! token || ! window.snap) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { variant: 'danger', message: 'Layanan pembayaran belum siap. Silakan muat ulang halaman.' },
        }));
        return;
    }

    const finish = (result) => {
        const url = new URL(redirectUrl, window.location.origin);
        url.searchParams.set('payment_result', result);
        window.location.assign(url.toString());
    };

    window.snap.pay(token, {
        onSuccess: () => finish('success'),
        onPending: () => finish('pending'),
        onError: () => finish('error'),
        onClose: () => finish('closed'),
    });
};
