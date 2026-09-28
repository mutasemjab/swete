<?php $__env->startSection('title', __('settings.users_list')); ?>
<?php $__env->startSection('breadcrumb', __('settings.users')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.users_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.users_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('settings.users.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-user-plus"></i>
        <?php echo e(__('settings.add_user')); ?>

    </a>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label' => __('settings.total_users'),  'value' => $totalUsers,         'icon' => 'users',         'color' => 'indigo'],
        ['label' => __('settings.active_users'),  'value' => $activeUsers,        'icon' => 'user-check',    'color' => 'emerald'],
        ['label' => __('settings.inactive_users'),'value' => $totalUsers - $activeUsers, 'icon' => 'user-xmark', 'color' => 'slate'],
        ['label' => __('settings.admins_count'),  'value' => $admins,             'icon' => 'user-shield',   'color' => 'amber'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-<?php echo e($stat['color']); ?>-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-<?php echo e($stat['icon']); ?> text-<?php echo e($stat['color']); ?>-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($stat['value']); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e($stat['label']); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<form method="GET" action="<?php echo e(route('settings.users.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   placeholder="<?php echo e(__('app.search')); ?>..."
                   class="form-input ps-10">
        </div>
    </div>
    <select name="role" class="form-select w-44">
        <option value=""><?php echo e(__('settings.all_roles')); ?></option>
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($role->name); ?>" <?php if(request('role') === $role->name): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="status" class="form-select w-44">
        <option value=""><?php echo e(__('app.all_statuses')); ?></option>
        <option value="1" <?php if(request('status') === '1'): echo 'selected'; endif; ?>><?php echo e(__('app.active')); ?></option>
        <option value="0" <?php if(request('status') === '0'): echo 'selected'; endif; ?>><?php echo e(__('app.inactive')); ?></option>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['search','role','status'])): ?>
        <a href="<?php echo e(route('settings.users.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>


<div class="card overflow-hidden">
    <?php if($users->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-users text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                <?php echo e(request()->hasAny(['search','role','status']) ? __('settings.no_users_search') : __('settings.no_users')); ?>

            </p>
            <?php if(!request()->hasAny(['search','role','status'])): ?>
                <a href="<?php echo e(route('settings.users.create')); ?>" class="btn-primary mt-5">
                    <i class="fa-solid fa-user-plus"></i>
                    <?php echo e(__('settings.add_first_user')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.user')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('app.phone')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.assign_role')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell"><?php echo e(__('settings.last_login')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br <?php echo e($user->avatar_gradient); ?>

                                            flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                                    <?php echo e($user->initials); ?>

                                </div>
                                <div>
                                    <a href="<?php echo e(route('settings.users.show', $user)); ?>"
                                       class="font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                                        <?php echo e($user->name); ?>

                                    </a>
                                    <p class="text-xs text-slate-400 mt-0.5" dir="ltr"><?php echo e($user->email); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600" dir="ltr"><?php echo e($user->phone ?? '—'); ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($user->roles->isNotEmpty()): ?>
                                <span class="badge bg-indigo-100 text-indigo-700">
                                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                    <?php echo e($user->roles->first()->name); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-slate-400 text-sm"><?php echo e(__('settings.no_role')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($user->status): ?>
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    <?php echo e(__('app.active')); ?>

                                </span>
                            <?php else: ?>
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    <?php echo e(__('app.inactive')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-slate-500">
                                <?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : __('settings.never_logged')); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('settings.users.show', $user)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="<?php echo e(__('app.view')); ?>">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('settings.users.edit', $user)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <?php if($user->id !== auth()->id()): ?>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', {
                                            action: '<?php echo e(route('settings.users.destroy', $user)); ?>',
                                            message: '<?php echo e(__('settings.delete_user_confirm')); ?> <?php echo e($user->name); ?>؟'
                                        })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($users->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($users->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($users->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($users->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($users->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>

                <?php if($users->hasMorePages()): ?>
                    <a href="<?php echo e($users->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/users/index.blade.php ENDPATH**/ ?>