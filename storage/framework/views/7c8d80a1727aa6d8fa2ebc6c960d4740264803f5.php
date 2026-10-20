<?php $customer = $customer ?? null; ?>
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-address-book text-blue-500 text-sm"></i>
            <?php echo e(__('accounting.customer')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label"><?php echo e(__('accounting.customer_name_or_company')); ?> <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="<?php echo e(old('name', $customer?->name)); ?>"
                   class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.customer_name_or_company_en')); ?></label>
            <input type="text" name="name_en" value="<?php echo e(old('name_en', $customer?->name_en)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_group')); ?></label>
            <select name="customer_group_id" class="form-select <?php $__errorArgs = ['customer_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('accounting.no_parent_group')); ?></option>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($group->id); ?>" <?php if(old('customer_group_id', $customer?->customer_group_id) == $group->id): echo 'selected'; endif; ?>><?php echo e($group->path); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['customer_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_phone')); ?></label>
            <input type="text" name="phone" value="<?php echo e(old('phone', $customer?->phone)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_email')); ?></label>
            <input type="email" name="email" value="<?php echo e(old('email', $customer?->email)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_tax_number')); ?></label>
            <input type="text" name="tax_number" value="<?php echo e(old('tax_number', $customer?->tax_number)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['tax_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['tax_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('clients.commercial_registration')); ?></label>
            <?php if($customer?->commercial_registration_url): ?>
                <a href="<?php echo e($customer->commercial_registration_url); ?>" target="_blank" class="block mb-2">
                    <img src="<?php echo e($customer->commercial_registration_url); ?>" class="h-20 rounded-lg object-cover border border-slate-200">
                </a>
            <?php endif; ?>
            <input type="file" name="commercial_registration_file" accept="image/*"
                   class="form-input <?php $__errorArgs = ['commercial_registration_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['commercial_registration_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('clients.personal_id')); ?></label>
            <?php if($customer?->personal_id_url): ?>
                <a href="<?php echo e($customer->personal_id_url); ?>" target="_blank" class="block mb-2">
                    <img src="<?php echo e($customer->personal_id_url); ?>" class="h-20 rounded-lg object-cover border border-slate-200">
                </a>
            <?php endif; ?>
            <input type="file" name="personal_id_file" accept="image/*"
                   class="form-input <?php $__errorArgs = ['personal_id_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['personal_id_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label"><?php echo e(__('accounting.party_address')); ?></label>
            <textarea name="address" rows="2" class="form-input <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('address', $customer?->address)); ?></textarea>
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_opening_balance')); ?></label>
            <input type="number" name="opening_balance" value="<?php echo e(old('opening_balance', $customer?->opening_balance ?? 0)); ?>"
                   step="0.001" dir="ltr" class="form-input <?php $__errorArgs = ['opening_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['opening_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       <?php if(old('status', $customer?->status ?? true)): echo 'checked'; endif; ?>>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
            </label>
        </div>
    </div>
</div>

<div class="card mb-5" x-data="{ contacts: <?php echo e(json_encode(old('contacts', $seedContacts))); ?> }">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-user-group text-teal-500 text-sm"></i>
            <?php echo e(__('clients.contacts_section')); ?>

        </h3>
        <button type="button" @click="contacts.push({ name: '', phone: '', job_title: '' })"
                class="btn-secondary btn-sm">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('clients.add_contact')); ?>

        </button>
    </div>
    <div class="px-6 py-5 space-y-4">
        <template x-for="(contact, index) in contacts" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl">
                <div>
                    <label class="form-label"><?php echo e(__('clients.contact_name')); ?></label>
                    <input type="text" :name="`contacts[${index}][name]`" x-model="contact.name"
                           class="form-input">
                </div>
                <div>
                    <label class="form-label"><?php echo e(__('clients.contact_phone')); ?></label>
                    <input type="text" :name="`contacts[${index}][phone]`" x-model="contact.phone"
                           class="form-input" dir="ltr">
                </div>
                <div>
                    <label class="form-label"><?php echo e(__('clients.contact_job_title')); ?></label>
                    <div class="flex gap-2">
                        <input type="text" :name="`contacts[${index}][job_title]`" x-model="contact.job_title"
                               class="form-input flex-1">
                        <button type="button" @click="contacts.splice(index, 1)"
                                class="btn-danger btn-sm self-end flex-shrink-0" title="<?php echo e(__('app.delete')); ?>">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <p x-show="contacts.length === 0" class="text-slate-400 text-sm text-center py-4"><?php echo e(__('clients.no_contacts')); ?></p>
    </div>
</div>

<?php if($extraFields->isNotEmpty()): ?>
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-amber-500 text-sm"></i>
            <?php echo e(__('clients.extra_fields_section')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <?php $__currentLoopData = $extraFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $efv = ($extraFieldValues ?? collect())->get($field->id); ?>
            <div class="<?php echo e(in_array($field->field_type, ['textarea', 'image']) ? 'sm:col-span-2' : ''); ?>">
                <label class="form-label"><?php echo e($field->localized_label); ?></label>
                <?php if($field->field_type === 'textarea'): ?>
                    <textarea name="extra_fields[<?php echo e($field->id); ?>]" rows="3"
                              class="form-input"><?php echo e(old("extra_fields.{$field->id}", $efv?->value)); ?></textarea>
                <?php elseif($field->field_type === 'image'): ?>
                    <?php if($efv?->file_path): ?>
                        <div class="mb-2">
                            <img src="<?php echo e(asset($efv->file_path)); ?>" alt="" class="h-24 rounded-lg object-cover border border-slate-200">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="extra_fields_images[<?php echo e($field->id); ?>]"
                           class="form-input <?php $__errorArgs = ["extra_fields_images.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           accept="image/*">
                <?php elseif($field->field_type === 'number'): ?>
                    <input type="number" name="extra_fields[<?php echo e($field->id); ?>]"
                           value="<?php echo e(old("extra_fields.{$field->id}", $efv?->value)); ?>" dir="ltr"
                           class="form-input">
                <?php elseif($field->field_type === 'date'): ?>
                    <input type="date" name="extra_fields[<?php echo e($field->id); ?>]"
                           value="<?php echo e(old("extra_fields.{$field->id}", $efv?->value)); ?>" dir="ltr"
                           class="form-input">
                <?php else: ?>
                    <input type="text" name="extra_fields[<?php echo e($field->id); ?>]"
                           value="<?php echo e(old("extra_fields.{$field->id}", $efv?->value)); ?>"
                           class="form-input">
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="card mb-5" x-data="{ show: false }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-lock text-indigo-500 text-sm"></i>
            <?php echo e(__('accounting.customer_portal_access')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label"><?php echo e(__('accounting.customer_portal_password')); ?></label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password"
                       class="form-input pe-10 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="<?php echo e(__('app.password_min')); ?>">
                <button type="button" @click="show = !show"
                        class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                </button>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('accounting.customer_portal_password_hint')); ?></p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-truck-ramp-box text-blue-500 text-sm"></i>
            <?php echo e(__('accounting.customer_default_shipping_address')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <p class="text-xs text-slate-400 sm:col-span-2"><?php echo e(__('accounting.customer_default_shipping_address_hint')); ?></p>

        <div>
            <label class="form-label"><?php echo e(__('app.address_line1')); ?></label>
            <input type="text" name="shipping_address_line1" value="<?php echo e(old('shipping_address_line1', $customer?->shipping_address_line1)); ?>"
                   class="form-input <?php $__errorArgs = ['shipping_address_line1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_address_line1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.address_line1_en')); ?></label>
            <input type="text" name="shipping_address_line1_en" value="<?php echo e(old('shipping_address_line1_en', $customer?->shipping_address_line1_en)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['shipping_address_line1_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_address_line1_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.po_box')); ?></label>
            <input type="text" name="shipping_po_box" value="<?php echo e(old('shipping_po_box', $customer?->shipping_po_box)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['shipping_po_box'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_po_box'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.postal_code')); ?></label>
            <input type="text" name="shipping_postal_code" value="<?php echo e(old('shipping_postal_code', $customer?->shipping_postal_code)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['shipping_postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.city')); ?></label>
            <input type="text" name="shipping_city" value="<?php echo e(old('shipping_city', $customer?->shipping_city)); ?>"
                   class="form-input <?php $__errorArgs = ['shipping_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.city_en')); ?></label>
            <input type="text" name="shipping_city_en" value="<?php echo e(old('shipping_city_en', $customer?->shipping_city_en)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['shipping_city_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_city_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.country')); ?></label>
            <input type="text" name="shipping_country" value="<?php echo e(old('shipping_country', $customer?->shipping_country)); ?>"
                   class="form-input <?php $__errorArgs = ['shipping_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('app.country_en')); ?></label>
            <input type="text" name="shipping_country_en" value="<?php echo e(old('shipping_country_en', $customer?->shipping_country_en)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['shipping_country_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['shipping_country_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/accounting/customers/_form.blade.php ENDPATH**/ ?>