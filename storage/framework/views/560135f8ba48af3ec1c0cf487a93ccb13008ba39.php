<?php $supplier = $supplier ?? null; ?>
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-truck text-blue-500 text-sm"></i>
            <?php echo e(__('accounting.supplier')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label"><?php echo e(__('accounting.supplier_name_or_company')); ?> <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="<?php echo e(old('name', $supplier?->name)); ?>"
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
            <label class="form-label"><?php echo e(__('accounting.supplier_name_or_company_en')); ?></label>
            <input type="text" name="name_en" value="<?php echo e(old('name_en', $supplier?->name_en)); ?>" dir="ltr"
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
            <select name="supplier_group_id" class="form-select <?php $__errorArgs = ['supplier_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('accounting.no_parent_group')); ?></option>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($group->id); ?>" <?php if(old('supplier_group_id', $supplier?->supplier_group_id) == $group->id): echo 'selected'; endif; ?>><?php echo e($group->path); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['supplier_group_id'];
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
            <input type="text" name="phone" value="<?php echo e(old('phone', $supplier?->phone)); ?>" dir="ltr"
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
            <input type="email" name="email" value="<?php echo e(old('email', $supplier?->email)); ?>" dir="ltr"
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
            <input type="text" name="tax_number" value="<?php echo e(old('tax_number', $supplier?->tax_number)); ?>" dir="ltr"
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
            <?php if($supplier?->commercial_registration_url): ?>
                <a href="<?php echo e($supplier->commercial_registration_url); ?>" target="_blank" class="block mb-2">
                    <img src="<?php echo e($supplier->commercial_registration_url); ?>" class="h-20 rounded-lg object-cover border border-slate-200">
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
            <?php if($supplier?->personal_id_url): ?>
                <a href="<?php echo e($supplier->personal_id_url); ?>" target="_blank" class="block mb-2">
                    <img src="<?php echo e($supplier->personal_id_url); ?>" class="h-20 rounded-lg object-cover border border-slate-200">
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
unset($__errorArgs, $__bag); ?>"><?php echo e(old('address', $supplier?->address)); ?></textarea>
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div x-data="{ scope: '<?php echo e(old('location_scope', $supplier?->location_scope ?? 'inside_jordan')); ?>' }" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('accounting.supplier_location_scope')); ?></label>
                <select name="location_scope" x-model="scope" class="form-select <?php $__errorArgs = ['location_scope'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="inside_jordan"><?php echo e(__('tenders.location_inside_jordan')); ?></option>
                    <option value="outside_jordan"><?php echo e(__('tenders.location_outside_jordan')); ?></option>
                </select>
                <?php $__errorArgs = ['location_scope'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div x-show="scope === 'inside_jordan'">
                <label class="form-label"><?php echo e(__('tenders.tender_governorate')); ?></label>
                <select name="governorate_id" class="js-select2 form-select <?php $__errorArgs = ['governorate_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $governorate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($governorate->id); ?>" <?php if(old('governorate_id', $supplier?->governorate_id) == $governorate->id): echo 'selected'; endif; ?>><?php echo e($governorate->localized_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['governorate_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div x-show="scope === 'outside_jordan'">
                <label class="form-label"><?php echo e(__('tenders.tender_country')); ?></label>
                <select name="country_id" class="js-select2 form-select <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($country->id); ?>" <?php if(old('country_id', $supplier?->country_id) == $country->id): echo 'selected'; endif; ?>><?php echo e($country->localized_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('accounting.party_opening_balance')); ?></label>
            <input type="number" name="opening_balance" value="<?php echo e(old('opening_balance', $supplier?->opening_balance ?? 0)); ?>"
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

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-blue-500 text-sm"></i>
            <?php echo e(__('accounting.supplier_shipping_instruction')); ?>

        </h3>
    </div>
    <div class="px-6 py-5">
        <textarea id="shipping_instruction" name="shipping_instruction" class="js-richtext form-input <?php $__errorArgs = ['shipping_instruction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('shipping_instruction', $supplier?->shipping_instruction)); ?></textarea>
        <?php $__errorArgs = ['shipping_instruction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        window.initRichText({
            images_upload_handler: function (blobInfo) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    fetch('<?php echo e(route('accounting.suppliers.shipping-instruction-image')); ?>', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: formData,
                    })
                        .then((res) => res.json())
                        .then((data) => resolve(data.location))
                        .catch(() => reject('<?php echo e(__('app.error_occurred')); ?>'));
                });
            },
        });
    </script>
<?php $__env->stopPush(); ?>

<div class="card mb-5 px-6 py-5">
    <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
        <input type="hidden" name="status" value="0">
        <input type="checkbox" name="status" value="1" class="sr-only peer"
               <?php if(old('status', $supplier?->status ?? true)): echo 'checked'; endif; ?>>
        <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                    peer-checked:bg-indigo-600 transition-all
                    after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                    after:bg-white after:rounded-full after:h-5 after:w-5
                    after:transition-all peer-checked:after:translate-x-full"></div>
        <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
    </label>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/accounting/suppliers/_form.blade.php ENDPATH**/ ?>