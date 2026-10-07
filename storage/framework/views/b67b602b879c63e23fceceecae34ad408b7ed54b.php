<style type="text/tailwindcss">
    * { font-family: 'Cairo', sans-serif; -webkit-tap-highlight-color: transparent; }
    body { background: #f1f5f9; overscroll-behavior-y: contain; }
    [x-cloak] { display: none !important; }

    .app-bar  { @apply sticky top-0 z-20 bg-slate-900 text-white px-4 py-4 flex items-center gap-3 shadow-md; }
    .m-card   { @apply bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4; }
    .m-label  { @apply block text-sm font-bold text-slate-700 mb-2; }
    .m-input  { @apply w-full px-4 py-3.5 border border-slate-200 rounded-2xl text-base text-slate-800 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all; }
    .m-btn-primary { @apply w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl font-bold text-base bg-indigo-600 text-white active:bg-indigo-800 shadow-lg shadow-indigo-500/30 disabled:opacity-40 transition-all; }
    .m-btn-outline { @apply flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-sm bg-white text-slate-700 border-2 border-slate-200 active:bg-slate-100 transition-all; }
    .m-chip { @apply w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0; }
    .m-save-ok { @apply text-emerald-500; }
    .m-save-pending { @apply text-slate-300; }
    .m-save-err { @apply text-rose-500; }
</style>


<style>
    .select2-container--default .select2-selection--single {
        height: 52px; border: 1px solid #e2e8f0; border-radius: 1rem;
        display: flex; align-items: center; padding: 0 1rem; background: #f8fafc;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #818cf8; box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2);
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding: 0; font-size: 1rem; color: #1e293b; line-height: 1.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px; }
    [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow { left: 0.75rem; right: auto; }
    .select2-dropdown { border-radius: 1rem; border-color: #e2e8f0; overflow: hidden; }
    .select2-search--dropdown { padding: 0.5rem; }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.75rem; border-color: #e2e8f0; padding: 0.6rem 0.75rem; outline: none; font-size: 1rem;
    }
    .select2-results__option { padding: 0.65rem 0.75rem; font-size: 0.95rem; }
    .select2-results__option--highlighted[aria-selected] { background-color: #4f46e5 !important; }
</style>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/_mobile-styles.blade.php ENDPATH**/ ?>