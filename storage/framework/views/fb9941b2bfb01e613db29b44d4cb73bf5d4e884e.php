<?php $purchaseRequest = $purchaseRequest ?? null; ?>
<div x-data="{
        linkType: '<?php echo e(old('link_type', $project ? 'project' : ($serviceCall ? 'service_call' : 'stock'))); ?>',
        scope: '<?php echo e(old('location_scope', $purchaseRequest?->location_scope ?? 'inside_jordan')); ?>',
        branches: <?php echo e($branches->map(fn ($b) => ['id' => $b->id, 'addressLines' => $b->localized_address_lines])->values()->toJson()); ?>,
        branchId: '<?php echo e(old('branch_id', $purchaseRequest?->branch_id ?? $branches->first()?->id)); ?>',
        get branchAddressLines() {
            const b = this.branches.find(x => String(x.id) === String(this.branchId));
            return b ? b.addressLines : [];
        },
        projectCustomers: <?php echo e($projects->pluck('customer_id', 'id')->toJson()); ?>,
        serviceCallCustomers: <?php echo e($serviceCalls->pluck('customer_id', 'id')->toJson()); ?>,
        customers: <?php echo e($customers->map(fn ($c) => [
            'id' => $c->id,
            'shipping_address_line1' => $c->shipping_address_line1,
            'shipping_address_line1_en' => $c->shipping_address_line1_en,
            'shipping_po_box' => $c->shipping_po_box,
            'shipping_postal_code' => $c->shipping_postal_code,
            'shipping_city' => $c->shipping_city,
            'shipping_city_en' => $c->shipping_city_en,
            'shipping_country' => $c->shipping_country,
            'shipping_country_en' => $c->shipping_country_en,
        ])->values()->toJson()); ?>,
        shipping: {
            address_line1: '<?php echo e(old('shipping_address_line1', $purchaseRequest?->shipping_address_line1)); ?>',
            address_line1_en: '<?php echo e(old('shipping_address_line1_en', $purchaseRequest?->shipping_address_line1_en)); ?>',
            po_box: '<?php echo e(old('shipping_po_box', $purchaseRequest?->shipping_po_box)); ?>',
            postal_code: '<?php echo e(old('shipping_postal_code', $purchaseRequest?->shipping_postal_code)); ?>',
            city: '<?php echo e(old('shipping_city', $purchaseRequest?->shipping_city)); ?>',
            city_en: '<?php echo e(old('shipping_city_en', $purchaseRequest?->shipping_city_en)); ?>',
            country: '<?php echo e(old('shipping_country', $purchaseRequest?->shipping_country)); ?>',
            country_en: '<?php echo e(old('shipping_country_en', $purchaseRequest?->shipping_country_en)); ?>',
        },
        fillShippingFromCustomer(customerId) {
            const c = this.customers.find(x => String(x.id) === String(customerId));
            if (!c) return;
            this.shipping.address_line1 = c.shipping_address_line1 || '';
            this.shipping.address_line1_en = c.shipping_address_line1_en || '';
            this.shipping.po_box = c.shipping_po_box || '';
            this.shipping.postal_code = c.shipping_postal_code || '';
            this.shipping.city = c.shipping_city || '';
            this.shipping.city_en = c.shipping_city_en || '';
            this.shipping.country = c.shipping_country || '';
            this.shipping.country_en = c.shipping_country_en || '';
        },
        onProjectChange(projectId) {
            const customerId = this.projectCustomers[projectId];
            if (customerId) this.fillShippingFromCustomer(customerId);
        },
        onServiceCallChange(serviceCallId) {
            const customerId = this.serviceCallCustomers[serviceCallId];
            if (customerId) this.fillShippingFromCustomer(customerId);
        },
        additionalNotes: <?php echo e((
            $purchaseRequest?->additionalNotes->map(fn ($n) => ['label' => $n->label, 'value' => $n->value])->values()
            ?? collect(\App\Models\PurchaseRequest::DEFAULT_NOTE_LABELS)->map(fn ($label) => ['label' => $label, 'value' => ''])
        )->toJson()); ?>,
        items: <?php echo e((
            $purchaseRequest?->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'quantity'    => (float) $i->quantity,
                'ercd'        => $i->ercd,
                'unit_price'  => (float) $i->unit_price,
                'features'    => $i->features->pluck('value')->values()->isNotEmpty() ? $i->features->pluck('value')->values() : [''],
            ])->values()
            ?? collect([['material_id' => '', 'quantity' => '', 'ercd' => '', 'unit_price' => '', 'features' => ['']]])
        )->toJson()); ?>,
        addItem() { this.items.push({ material_id: '', quantity: '', ercd: '', unit_price: '', features: [''] }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-cyan-500 text-sm"></i>
                <?php echo e(__('external_purchases.purchase_request')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label"><?php echo e(__('external_purchases.request_link_type')); ?></label>
                <select x-model="linkType" class="form-select">
                    <option value="project"><?php echo e(__('external_purchases.link_type_project')); ?></option>
                    <option value="service_call"><?php echo e(__('external_purchases.link_type_service_call')); ?></option>
                    <option value="stock"><?php echo e(__('external_purchases.link_type_stock')); ?></option>
                </select>
            </div>

            <div x-show="linkType === 'project'">
                <label class="form-label"><?php echo e(__('external_purchases.request_project')); ?></label>
                <select name="project_id" @change="onProjectChange($event.target.value)" class="js-select2 form-select <?php $__errorArgs = ['project_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php if(old('project_id', $project?->id) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->number); ?> — <?php echo e($p->localized_title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['project_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div x-show="linkType === 'service_call'">
                <label class="form-label"><?php echo e(__('external_purchases.request_service_call')); ?></label>
                <select name="service_call_id" @change="onServiceCallChange($event.target.value)" class="js-select2 form-select <?php $__errorArgs = ['service_call_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $serviceCalls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sc->id); ?>" <?php if(old('service_call_id', $serviceCall?->id) == $sc->id): echo 'selected'; endif; ?>><?php echo e($sc->number); ?> — <?php echo e($sc->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['service_call_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('external_purchases.request_date')); ?> <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="<?php echo e(old('date', $purchaseRequest?->date?->toDateString() ?? now()->toDateString())); ?>"
                       class="form-input <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('external_purchases.request_supplier')); ?> <span class="text-rose-500">*</span></label>
                <select name="supplier_id" class="js-select2 form-select <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>" <?php if(old('supplier_id', $purchaseRequest?->supplier_id) == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->localized_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('external_purchases.request_currency')); ?></label>
                <select name="currency_id" class="js-select2 form-select <?php $__errorArgs = ['currency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($currency->id); ?>" <?php if(old('currency_id', $purchaseRequest?->currency_id) == $currency->id): echo 'selected'; endif; ?>><?php echo e($currency->localized_name); ?> (<?php echo e($currency->code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['currency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('external_purchases.request_branch')); ?> <span class="text-rose-500">*</span></label>
                <select name="branch_id" x-model="branchId" class="js-select2 form-select <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>" <?php if(old('branch_id', $purchaseRequest?->branch_id ?? $branches->first()?->id) == $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->localized_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('external_purchases.request_address')); ?></label>
                <div class="form-input bg-slate-50 text-slate-600 h-auto py-2.5 leading-6">
                    <template x-for="line in branchAddressLines" :key="line">
                        <p x-text="line"></p>
                    </template>
                    <p x-show="branchAddressLines.length === 0">—</p>
                </div>
            </div>

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label"><?php echo e(__('external_purchases.request_shipping_address')); ?></label>
                    <p class="text-xs text-slate-400 -mt-1 mb-1"><?php echo e(__('external_purchases.request_shipping_address_hint')); ?></p>
                </div>

                <div>
                    <label class="form-label"><?php echo e(__('app.address_line1')); ?></label>
                    <input type="text" name="shipping_address_line1" x-model="shipping.address_line1"
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
                    <input type="text" name="shipping_address_line1_en" x-model="shipping.address_line1_en" dir="ltr"
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
                    <input type="text" name="shipping_po_box" x-model="shipping.po_box" dir="ltr"
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
                    <input type="text" name="shipping_postal_code" x-model="shipping.postal_code" dir="ltr"
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
                    <input type="text" name="shipping_city" x-model="shipping.city"
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
                    <input type="text" name="shipping_city_en" x-model="shipping.city_en" dir="ltr"
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
                    <input type="text" name="shipping_country" x-model="shipping.country"
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
                    <input type="text" name="shipping_country_en" x-model="shipping.country_en" dir="ltr"
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

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label"><?php echo e(__('external_purchases.request_location_scope')); ?></label>
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
                    <select name="governorate" class="js-select2 form-select <?php $__errorArgs = ['governorate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value=""><?php echo e(__('app.select')); ?></option>
                        <?php $__currentLoopData = \App\Models\Tender::JORDAN_GOVERNORATES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $names): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(old('governorate', $purchaseRequest?->governorate) === $key): echo 'selected'; endif; ?>><?php echo e($names[app()->getLocale() === 'en' ? 'en' : 'ar']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['governorate'];
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
                            <option value="<?php echo e($country->id); ?>" <?php if(old('country_id', $purchaseRequest?->country_id) == $country->id): echo 'selected'; endif; ?>><?php echo e($country->localized_name); ?></option>
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

            <div class="sm:col-span-2">
                <label class="form-label"><?php echo e(__('external_purchases.request_notes')); ?></label>
                <textarea name="notes" rows="2" class="form-input <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $purchaseRequest?->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
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

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-cyan-500 text-sm"></i>
                <?php echo e(__('external_purchases.request_items')); ?>

            </h3>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('accounting.invoice_add_item')); ?>

            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.material')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28"><?php echo e(__('external_purchases.item_ercd')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-56"><?php echo e(__('external_purchases.item_features')); ?></th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select" required>
                                    <option value=""><?php echo e(__('app.select')); ?></option>
                                    <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($material->id); ?>"><?php echo e($material->localized_name); ?> (<?php echo e($material->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="text" :name="`items[${index}][ercd]`" x-model="item.ercd" dir="ltr" class="form-input">
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                       step="0.001" min="0" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="space-y-1">
                                    <template x-for="(feature, fIndex) in item.features" :key="fIndex">
                                        <div class="flex items-center gap-1">
                                            <input type="text" :name="`items[${index}][features][${fIndex}]`" x-model="item.features[fIndex]"
                                                   class="form-input !py-1 !text-xs" placeholder="<?php echo e(__('external_purchases.item_feature_placeholder')); ?>">
                                            <button type="button" @click="item.features.splice(fIndex, 1)"
                                                    class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="item.features.push('')"
                                            class="text-xs font-semibold text-indigo-600 hover:underline">
                                        <i class="fa-solid fa-plus"></i> <?php echo e(__('external_purchases.add_feature')); ?>

                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="<?php echo e(__('accounting.invoice_remove_item')); ?>"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-note-sticky text-cyan-500 text-sm"></i>
                <?php echo e(__('external_purchases.additional_notes')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 space-y-3">
            <template x-for="(note, nIndex) in additionalNotes" :key="nIndex">
                <div class="flex items-center gap-3">
                    <span class="w-56 flex-shrink-0 text-sm font-semibold text-slate-700" x-text="note.label"></span>
                    <input type="hidden" :name="`additional_notes[${nIndex}][label]`" :value="note.label">
                    <input type="text" :name="`additional_notes[${nIndex}][value]`" x-model="note.value" dir="ltr" class="form-input">
                    <button type="button" @click="additionalNotes.splice(nIndex, 1)"
                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </template>
            <p x-show="additionalNotes.length === 0" class="text-sm text-slate-400"><?php echo e(__('external_purchases.no_additional_notes')); ?></p>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/external-purchases/purchase-requests/_form.blade.php ENDPATH**/ ?>