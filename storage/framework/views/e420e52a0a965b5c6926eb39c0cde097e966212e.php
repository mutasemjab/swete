<?php $__env->startSection('title', __('settings.approval_rules_list')); ?>
<?php $__env->startSection('breadcrumb', __('settings.approval_rules')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.approval_rules_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.approval_rules_subtitle')); ?></p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-user-shield text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($rules->where('status', true)->count()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('settings.active_approval_rules')); ?></p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.approval_rule_route')); ?></th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.approval_rule_label')); ?></th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.approval_rule_approvers')); ?></th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.approval_rule_enabled')); ?></th>
                    <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $routeNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $rule = $rules->get($routeName); ?>
                <tr>
                    <form action="<?php echo e(route('settings.approval-rules.store')); ?>" method="POST" class="contents">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="route_name" value="<?php echo e($routeName); ?>">
                        <td class="px-5 py-3">
                            <span class="font-mono text-sm text-slate-700 bg-slate-100 px-2 py-1 rounded-lg"><?php echo e($routeName); ?></span>
                        </td>
                        <td class="px-5 py-3">
                            <input type="text" name="label" value="<?php echo e($rule?->label); ?>"
                                   placeholder="<?php echo e(__('settings.approval_rule_label_placeholder')); ?>"
                                   class="form-input !py-1.5 !text-sm w-40">
                        </td>
                        <td class="px-5 py-3">
                            <select name="approver_ids[]" multiple class="form-select !py-1.5 !text-sm min-w-40" size="2">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php if($rule && $rule->approvers->contains('id', $user->id)): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="px-5 py-3">
                            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" name="status" value="1" class="sr-only peer" <?php if($rule?->status): echo 'checked'; endif; ?>>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                            peer-checked:bg-indigo-600 transition-all
                                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                            after:bg-white after:rounded-full after:h-5 after:w-5
                                            after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </td>
                        <td class="px-5 py-3 text-end">
                            <button type="submit" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <?php echo e(__('app.save')); ?>

                            </button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/approval-rules/index.blade.php ENDPATH**/ ?>