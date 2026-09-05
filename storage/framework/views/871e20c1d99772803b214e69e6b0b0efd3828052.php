<?php $__env->startSection('title', __('approvals.my_approvals')); ?>
<?php $__env->startSection('breadcrumb', __('approvals.my_approvals')); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ tab: '<?php echo e($tab === 'by_me' ? 'by_me' : 'for_me'); ?>' }">

    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e(__('approvals.my_approvals')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('approvals.my_approvals_subtitle')); ?></p>
        </div>
    </div>

    
    <div class="flex gap-2 mb-6">
        <button type="button" @click="tab = 'for_me'"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all"
                :class="tab === 'for_me' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            <?php echo e(__('approvals.tab_for_me')); ?>

            <?php if($pendingForMe->count()): ?>
                <span class="ms-1.5 px-1.5 py-0.5 rounded-md text-[11px]" :class="tab === 'for_me' ? 'bg-white/20' : 'bg-rose-100 text-rose-600'"><?php echo e($pendingForMe->count()); ?></span>
            <?php endif; ?>
        </button>
        <button type="button" @click="tab = 'by_me'"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all"
                :class="tab === 'by_me' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            <?php echo e(__('approvals.tab_by_me')); ?>

        </button>
    </div>

    
    <div x-show="tab === 'for_me'" class="card overflow-hidden">
        <?php if($pendingForMe->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-clipboard-check text-slate-400 text-3xl"></i>
                </div>
                <p class="text-slate-800 font-bold text-lg"><?php echo e(__('approvals.no_pending_for_me')); ?></p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.item')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.requested_by')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('approvals.note')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.requested_at')); ?></th>
                            <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $pendingForMe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-800"><?php echo e($approval->approvable_label); ?></td>
                            <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($approval->requester?->name); ?></td>
                            <td class="px-5 py-4 text-sm text-slate-500 hidden md:table-cell"><?php echo e($approval->note ?? '—'); ?></td>
                            <td class="px-5 py-4 text-sm text-slate-500"><?php echo e($approval->created_at->diffForHumans()); ?></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="<?php echo e(route('approvals.approve', $approval)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-primary btn-sm">
                                            <i class="fa-solid fa-check"></i> <?php echo e(__('approvals.approve')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('approvals.reject', $approval)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-danger btn-sm">
                                            <i class="fa-solid fa-xmark"></i> <?php echo e(__('approvals.reject')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    
    <div x-show="tab === 'by_me'" class="card overflow-hidden">
        <?php if($submittedByMe->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-paper-plane text-slate-400 text-3xl"></i>
                </div>
                <p class="text-slate-800 font-bold text-lg"><?php echo e(__('approvals.no_submitted_by_me')); ?></p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.item')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.requested_to')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('approvals.decision_note')); ?></th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.requested_at')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $submittedByMe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-800"><?php echo e($approval->approvable_label); ?></td>
                            <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($approval->approver?->name); ?></td>
                            <td class="px-5 py-4">
                                <?php echo $__env->make('components.approval-badge', ['status' => $approval->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php if($approval->approvable instanceof \App\Models\PendingAction && $approval->approvable->status === 'failed'): ?>
                                    <span class="badge bg-rose-100 text-rose-700 ms-1" title="<?php echo e($approval->approvable->error_message); ?>">
                                        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo e(__('approvals.action_failed')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500 hidden md:table-cell"><?php echo e($approval->decision_note ?? '—'); ?></td>
                            <td class="px-5 py-4 text-sm text-slate-500"><?php echo e($approval->created_at->diffForHumans()); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/approvals/index.blade.php ENDPATH**/ ?>