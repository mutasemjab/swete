{{--
    Reusable "quick add a customer" modal — lets a Price Quote opened from inside a tender
    (price-quotes/create?tender_id=X) add a brand-new customer without leaving the form. Creates
    the customer via AJAX (Accounting\CustomerController@store, which already supports
    wantsJson()), then injects the new option into the matching <select> and selects it.

    Props:
    - $targetSelector  CSS selector matching the <select> to inject the new option into,
                        e.g. 'select[name="customer_id"]'
    - $label           button/modal label, e.g. __('accounting.add_customer_quick')
--}}
<div x-data="{
        open: false,
        saving: false,
        error: '',
        name: '',
        phone: '',
        submit() {
            this.saving = true;
            this.error = '';
            fetch('{{ route('accounting.customers.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    name: this.name,
                    phone: this.phone,
                }),
            })
                .then(async (res) => {
                    if (!res.ok) throw await res.json();
                    return res.json();
                })
                .then((data) => {
                    document.querySelectorAll('{{ $targetSelector }}').forEach((select) => {
                        const option = new Option(data.name + ' (' + data.code + ')', data.id, true, true);
                        select.appendChild(option);
                        $(select).val(data.id).trigger('change').trigger('change.select2');
                    });
                    window.dispatchEvent(new CustomEvent('customer-quick-added', {
                        detail: { id: data.id, label: data.name + ' (' + data.code + ')' },
                    }));
                    this.open = false;
                    this.name = '';
                    this.phone = '';
                })
                .catch((err) => { this.error = err.message || '{{ __('app.error_occurred') }}'; })
                .finally(() => { this.saving = false; });
        },
     }"
     class="inline-block">
    <button type="button" @click="open = true" class="btn-secondary btn-sm">
        <i class="fa-solid fa-plus"></i>
        {{ $label }}
    </button>

    <div x-show="open" x-cloak
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="bg-white rounded-3xl shadow-2xl shadow-slate-900/20 p-6 max-w-sm w-full">
            <h3 class="text-lg font-black text-slate-800 mb-1.5">{{ $label }}</h3>

            <label class="form-label">{{ __('accounting.customer_name_or_company') }}</label>
            <input type="text" x-model="name" @keydown.enter.prevent="submit()" class="form-input mb-3">

            <label class="form-label">{{ __('app.phone') }}</label>
            <input type="text" x-model="phone" dir="ltr" @keydown.enter.prevent="submit()" class="form-input">

            <p class="form-error" x-show="error" x-text="error"></p>
            <div class="flex gap-3 mt-5">
                <button type="button" @click="open = false" class="btn-secondary flex-1">{{ __('app.cancel') }}</button>
                <button type="button" :disabled="saving || !name" @click="submit()" class="btn-primary flex-1">
                    <span x-show="!saving">{{ __('app.save') }}</span>
                    <span x-show="saving">…</span>
                </button>
            </div>
        </div>
    </div>
</div>
