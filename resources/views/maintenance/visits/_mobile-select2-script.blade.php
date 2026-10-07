<script>
    window.initSelect2 = function (context) {
        (context ? $(context) : $(document)).find('.js-select2').each(function () {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2({
                dir: '{{ $isRtl ? "rtl" : "ltr" }}',
                width: '100%',
                dropdownAutoWidth: false,
                placeholder: $el.data('placeholder') || $el.find('option[value=""]').first().text() || '',
                allowClear: $el.find('option[value=""]').length > 0 && !$el.prop('required'),
            });
            // select2's own change events don't always reliably reach a vanilla-listener
            // framework like Alpine — re-dispatch a native 'change' explicitly.
            $el.on('select2:select select2:unselect select2:clear', function () {
                this.dispatchEvent(new Event('change'));
            });
        });
    };
    document.addEventListener('DOMContentLoaded', () => window.initSelect2());
</script>
