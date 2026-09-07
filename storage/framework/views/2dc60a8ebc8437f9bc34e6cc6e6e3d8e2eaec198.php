<?php $__env->startSection('title', $tender->number); ?>
<?php $__env->startSection('breadcrumb', $tender->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($tender->number); ?>

            <span class="badge bg-<?php echo e($tender->statusRef?->color ?? 'slate'); ?>-100 text-<?php echo e($tender->statusRef?->color ?? 'slate'); ?>-700"><?php echo e($tender->statusRef?->localized_name); ?></span>
        </h1>
        <p class="page-subtitle"><?php echo e($tender->localized_title); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <form action="<?php echo e(route('tenders.convert-to-project', $tender)); ?>" method="POST"
              @submit="if (! confirm('<?php echo e(__('tenders.convert_to_project_confirm')); ?>')) $event.preventDefault()">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-diagram-project"></i>
                <?php echo e(__('tenders.convert_to_project')); ?>

            </button>
        </form>
        <a href="<?php echo e(route('tenders.edit', $tender)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('tenders.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_entity_name')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->localized_entity_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->party?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_submission_deadline')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->submission_deadline->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_location_scope')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($tender->location_scope === 'outside_jordan'): ?>
                    <?php echo e(__('tenders.location_outside_jordan')); ?> — <?php echo e($tender->country?->localized_name); ?>

                <?php else: ?>
                    <?php echo e(__('tenders.location_inside_jordan')); ?> — <?php echo e($tender->localized_governorate); ?>

                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_delivery_terms')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->delivery_terms ? __('tenders.delivery_terms_' . $tender->delivery_terms) : '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_coverage')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->coverage ? __('tenders.coverage_' . $tender->coverage) : '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_tax_exempt')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->tax_exempt ? __('app.yes') : __('app.no')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_customs_exempt')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->customs_exempt ? __('app.yes') : __('app.no')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_win_probability')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($tender->win_probability !== null ? $tender->win_probability . '%' : '—'); ?></dd>
        </div>
        <?php if($tender->description): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_description')); ?></dt>
            <dd class="text-slate-700"><?php echo e($tender->description); ?></dd>
        </div>
        <?php endif; ?>
        <?php if($tender->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($tender->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-2"><?php echo e(__('tenders.tender_documents_url')); ?></h3>
        <?php if($tender->documents_url): ?>
            <a href="<?php echo e($tender->documents_url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i><?php echo e($tender->documents_url); ?>

            </a>
        <?php else: ?>
            <p class="text-sm text-slate-400">—</p>
        <?php endif; ?>
    </div>
    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-2"><?php echo e(__('tenders.tender_design_documents_url')); ?></h3>
        <?php if($tender->design_documents_url): ?>
            <a href="<?php echo e($tender->design_documents_url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i><?php echo e($tender->design_documents_url); ?>

            </a>
        <?php else: ?>
            <p class="text-sm text-slate-400">—</p>
        <?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.price_quotes')); ?></h3>
        <a href="<?php echo e(route('price-quotes.create', ['tender_id' => $tender->id])); ?>" class="btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('tenders.add_quote')); ?>

        </a>
    </div>

    <?php if($tender->priceQuotes->isNotEmpty()): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_number')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_date')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_total')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $tender->priceQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="<?php echo e(route('price-quotes.show', $quote)); ?>" class="font-mono font-bold text-indigo-600 hover:underline"><?php echo e($quote->number); ?></a>
                        </td>
                        <td class="px-5 py-3 text-slate-600"><?php echo e($quote->date->format('Y-m-d')); ?></td>
                        <td class="px-5 py-3 font-bold text-slate-800"><?php echo e(number_format($quote->total, 3)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="px-6 py-6 text-sm text-slate-400"><?php echo e(__('tenders.no_quotes')); ?></p>
    <?php endif; ?>

    <?php if($unlinkedQuotes->isNotEmpty()): ?>
    <form action="<?php echo e(route('tenders.attach-quote', $tender)); ?>" method="POST" class="px-6 py-4 border-t border-slate-100 flex items-end gap-3 flex-wrap">
        <?php echo csrf_field(); ?>
        <div class="min-w-56">
            <label class="form-label"><?php echo e(__('tenders.attach_existing_quote')); ?></label>
            <select name="price_quote_id" class="js-select2 form-select" required>
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $unlinkedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($quote->id); ?>"><?php echo e($quote->number); ?> — <?php echo e($quote->customer?->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn-secondary">
            <i class="fa-solid fa-link"></i>
            <?php echo e(__('tenders.attach_quote')); ?>

        </button>
    </form>
    <?php endif; ?>
</div>

<?php if($tender->projects->isNotEmpty()): ?>
<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.projects')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_number')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_title')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_status')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $tender->projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <a href="<?php echo e(route('projects.show', $project)); ?>" class="font-mono font-bold text-indigo-600 hover:underline"><?php echo e($project->number); ?></a>
                    </td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e($project->localized_title); ?></td>
                    <td class="px-5 py-3">
                        <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('tenders.project_status_' . $project->status)); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/show.blade.php ENDPATH**/ ?>