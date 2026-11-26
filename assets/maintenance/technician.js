import {openWorkspace, read, write, all, remove, syncVisit} from './storage.js';
import {buildChecklist, defaultUnitConfig, unitConfigFromValues, unitConfigValues, FORM_STEPS} from './reference/checklists.js';
import {MACHINE_TYPES} from './reference/machine-types.js';
import {MAINTENANCE_TYPES, maintenanceLabel} from './reference/company.js';

const config = JSON.parse(document.getElementById('technician-config').textContent);
const main = document.getElementById('workspace');
let db, data, visits = [], current = null, reportIndex = 0, step = 1, tab = 'visits';
let locale = config.locale, syncing = false, saveChain = Promise.resolve(), saveError = null, lookup = null;
const t = (ar, en) => locale === 'ar' ? ar : en;
const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const uuid = () => crypto.randomUUID ? crypto.randomUUID() : '10000000-1000-4000-8000-100000000000'.replace(/[018]/g,c=>(Number(c)^crypto.getRandomValues(new Uint8Array(1))[0]&15>>Number(c)/4).toString(16));
const now = () => new Date().toISOString();
const today = () => { const d = new Date(); return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; };
const clone = value => structuredClone(value);
const options = (rows, value, blank = true) => `${blank ? '<option value="">—</option>' : ''}${rows.map(row => `<option value="${escape(row.id)}" ${String(row.id) === String(value) ? 'selected' : ''}>${escape(row.name)}</option>`).join('')}`;
const button = (action, label, primary = false, extra = '') => `<button type="button" class="button ${primary ? 'primary' : ''}" data-action="${action}" ${extra}>${label}</button>`;
const input = (label, key, value, type = 'text', extra = '') => `<div><label>${label}</label><input data-key="${key}" type="${type}" value="${escape(value)}" ${extra}></div>`;
const textarea = (label, key, value) => `<div class="wide"><label>${label}</label><textarea data-key="${key}">${escape(value)}</textarea></div>`;
const report = () => current?.reports[reportIndex];
const customerName = id => data?.customers.find(c => c.id === Number(id))?.name || '';
const locked = () => current?.state !== 'draft';

function notice(message = '', error = false) {
    document.getElementById('notice').className = message ? `alert ${error ? 'error' : ''}` : '';
    document.getElementById('notice').textContent = message;
}

function localSave() {
    if (!current) return Promise.resolve();
    current.updated_at = now();
    const snapshot = clone(current);
    saveChain = saveChain.catch(() => {}).then(() => write(db, 'visits', snapshot.uuid, snapshot)).then(() => {
        saveError = null;
        const label = document.getElementById('save-state');
        if (label) label.textContent = t('محفوظ على الجهاز', 'Saved on this device');
    }).catch(error => {
        saveError = error;
        notice(t('تعذّر الحفظ على الجهاز. لا تغلق الصفحة؛ قد تكون مساحة التخزين ممتلئة.', 'Local save failed. Keep this page open; device storage may be full.'), true);
        throw error;
    });
    saveChain.catch(() => {});
    return saveChain;
}

async function api(path, payload) {
    const response = await fetch(config.base + path, {
        method: payload ? 'POST' : 'GET', credentials: 'same-origin', cache: 'no-store',
        headers: {'Accept': 'application/json', ...(payload ? {'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf} : {})},
        ...(payload ? {body: JSON.stringify(payload)} : {}), signal: AbortSignal.timeout(payload ? 120000 : 25000),
    });
    const result = response.headers.get('Content-Type')?.includes('application/json') ? await response.json() : null;
    if (!response.ok || response.redirected || !result) {
        if ([401, 403, 419].includes(response.status) || response.redirected) throw new Error(t('الجلسة انتهت أو الصلاحية غير متاحة. سجّل الدخول بنفس الحساب ثم أعد المزامنة؛ التقارير محفوظة.', 'Session expired or access denied. Sign in with the same account and sync again; reports are saved.'));
        throw new Error(Object.values(result?.errors || {}).flat().join('\n') || result?.message || `HTTP ${response.status}`);
    }
    return result;
}

async function refreshData() {
    const fresh = await api('/offline-data');
    if (fresh.user.id !== config.user.id) throw new Error(t('الحساب الحالي مختلف. افتح الواجهة من جديد قبل المزامنة.', 'The account has changed. Reopen the portal before syncing.'));
    config.csrf = fresh.csrf;
    await write(db, 'meta', 'data', fresh);
    data = fresh;
    visits = await all(db, 'visits');
    for (const visit of data.open_visits) {
        if (visits.some(v => v.existing_visit_id === visit.id)) continue;
        const draft = {
            uuid: uuid(), user_id: config.user.id, state: 'draft', existing_visit_id: visit.id,
            customer_id: visit.customer_id, contract_id: visit.contract_id, kind: 'periodic',
            maintenance_request_id: visit.maintenance_request_id, check_in_at: visit.check_in_at, updated_at: now(),
            reports: visit.reports.map(r => ({...r, uuid: uuid(), existing_report_id: r.id, maintenance_type: 'Preventive',
                machine_type: 'custom', photos: [], fields: r.fields.map(f => ({...f, answer: f.answer ?? ''}))})),
        };
        await write(db, 'visits', draft.uuid, draft);
        visits.push(draft);
    }
}

async function synchronize() {
    if (syncing || !navigator.onLine) return;
    syncing = true; connection();
    const work = async () => {
        await refreshData();
        for (const visit of await all(db, 'visits')) {
            if (visit.state !== 'queued') continue;
            try {
                const saved = await syncVisit(visit, payload => api('/sync', payload), value => write(db, 'visits', value.uuid, value));
                if (current?.uuid === saved.uuid) current = saved;
            } catch (error) {
                visit.syncError = error.message;
                await write(db, 'visits', visit.uuid, visit);
                if (current?.uuid === visit.uuid) current.syncError = error.message;
                notice(error.message, true);
            }
        }
        visits = await all(db, 'visits');
    };
    try {
        // A second open tab must not overwrite the first tab's sync receipt.
        if (navigator.locks) await navigator.locks.request(`asee-sync-${config.user.id}`, work);
        else await work();
    } catch (error) { notice(error.message, true); }
    finally { syncing = false; connection(); if (!current || locked()) render(); }
}

function connection() {
    document.getElementById('connection').textContent = syncing ? t('جاري المزامنة…', 'Syncing…') : navigator.onLine ? t('متصل', 'Online') : t('بدون إنترنت', 'Offline');
}

function nav() {
    const tabs = [['visits','الزيارات','Visits'],['reports','التقارير','Reports'],['equipment','الأجهزة','Equipment'],['documents','المانيوالات','Manuals'],['alarms','الأكواد والبارامترات','Codes & parameters'],['settings','الإعدادات','Settings']];
    document.getElementById('navigation').innerHTML = tabs.map(([key,ar,en]) => `<button data-tab="${key}" class="${key === tab ? 'active' : ''}">${t(ar,en)}</button>`).join('');
}

function render() {
    nav(); connection();
    if (!data) { main.innerHTML = `<div class="card empty">${t('اتصل بالإنترنت أول مرة لتحميل العملاء والأجهزة والنماذج.', 'Connect once to download customers, equipment and forms.')}<p>${button('sync',t('إعادة المحاولة','Retry'),true)}</p></div>`; return; }
    if (current) return renderVisit();
    if (tab === 'visits') return renderHome();
    if (tab === 'reports') return renderReports();
    if (tab === 'equipment') return renderEquipment();
    if (tab === 'documents') return renderDocuments();
    if (tab === 'alarms') return renderAlarms();
    renderSettings();
}

function renderHome() {
    const queued = visits.filter(v => v.state === 'queued');
    main.innerHTML = `<div class="page-title"><div><h1>${t('مساحة الفني','Technician workspace')}</h1><span class="muted">${escape(config.user.name)}</span></div>${button('new',t('+ زيارة جديدة','+ New visit'),true)}</div>
    <div class="grid"><div class="card"><div class="stat">${visits.filter(v=>v.state==='draft').length}</div>${t('زيارات قيد العمل','Visits in progress')}</div><div class="card"><div class="stat">${queued.length}</div>${t('زيارات موقعة بانتظار المزامنة','Signed visits waiting to sync')}<p>${button('sync',t('مزامنة الآن','Sync now'))}</p></div></div>
    <h2>${t('زياراتي المحفوظة','My saved visits')}</h2><div class="report-grid">${visits.slice().sort((a,b)=>b.updated_at.localeCompare(a.updated_at)).map(visitCard).join('') || `<div class="empty">${t('لا توجد زيارات محفوظة بعد','No saved visits yet')}</div>`}</div>
    <h2 style="margin-top:28px">${t('الزيارات المجدولة','Scheduled visits')}</h2><div class="report-grid">${data.schedules.filter(s=>!visits.some(v=>v.scheduled_visit_id===s.id)).map(s => `<div class="card"><span class="badge">${escape(s.date)}</span><h3>${escape(customerName(s.customer_id))}</h3><p>${escape(data.units.find(u=>u.id===s.unit_id)?.name || '')}</p>${button('scheduled',t('بدء الزيارة','Start visit'),false,`data-id="${s.id}"`)}</div>`).join('') || `<p class="muted">${t('لا توجد زيارات مجدولة متاحة','No available scheduled visits')}</p>`}</div>`;
}

function visitCard(v) {
    const labels = {draft:t('مسودة','Draft'),queued:t('موقّع · بانتظار المزامنة','Signed · awaiting sync'),synced:t('تم الإرسال للمراجعة','Sent for review')};
    return `<div class="card"><span class="badge ${v.state==='synced'?'green':'orange'}">${labels[v.state]}</span><h3>${escape(customerName(v.customer_id))}</h3><p class="muted">${escape(v.check_in_at?.slice(0,10))} · ${v.reports.length} ${t('تقارير','reports')}</p>${v.syncError?`<p class="lookup-text" style="color:#a12834">${escape(v.syncError)}</p>`:''}${button('open',t('فتح الزيارة','Open visit'),false,`data-id="${v.uuid}"`)}</div>`;
}

async function newVisit(schedule = null) {
    current = {uuid:uuid(),user_id:config.user.id,state:'draft',customer_id:schedule?.customer_id || '',contract_id:schedule?.contract_id || null,
        scheduled_visit_id:schedule?.id || null,kind:schedule?.kind || 'periodic',check_in_at:now(),updated_at:now(),reports:[]};
    reportIndex = 0; step = 1; tab = 'visits';
    await localSave(); render();
}

function renderVisit() {
    const r = report();
    main.innerHTML = `<div class="page-title"><div><h1>${t('زيارة صيانة','Maintenance visit')}</h1><span class="save-state" id="save-state">${locked()?t('موقّع ومقفل على الجهاز','Signed and locked on device'):t('محفوظ على الجهاز','Saved on this device')}</span></div>${button('back',t('رجوع','Back'))}</div>
    ${current.syncError?`<div class="alert error">${escape(current.syncError)}</div>`:''}
    <section class="card"><fieldset ${locked()?'disabled':''}><div class="grid">
    <div><label>${t('العميل','Customer')}</label><select data-visit="customer_id" ${current.existing_visit_id||current.scheduled_visit_id?'disabled':''}>${options(data.customers,current.customer_id)}</select></div>
    <div><label>${t('العقد','Contract')}</label><select data-visit="contract_id" ${current.scheduled_visit_id?'disabled':''}>${options(data.contracts.filter(c=>c.customer_id===Number(current.customer_id)),current.contract_id).replace('<option value="">—</option>',`<option value="">${t('بدون عقد','No contract')}</option>`)}</select></div>
    <div><label>${t('نوع الزيارة','Visit kind')}</label><select data-visit="kind" ${current.scheduled_visit_id?'disabled':''}>${options([{id:'periodic',name:t('دورية / عادية','Periodic / regular')},{id:'emergency',name:t('طوارئ','Emergency')}],current.kind,false)}</select></div>
    <div><label>${t('طلب الصيانة','Maintenance request')}</label><select data-visit="maintenance_request_id">${options(data.requests.filter(x=>x.customer_id===Number(current.customer_id)).map(x=>({id:x.id,name:x.description})),current.maintenance_request_id)}</select></div>
    </div></fieldset>${allowanceHint()}</section>
    <section class="card"><div class="actions">${current.reports.map((item,i)=>button('report',escape(item.title),i===reportIndex,`data-id="${i}"`)).join('')}${!locked()?button('add-report',t('+ تقرير جهاز','+ Equipment report')):''}</div></section>
    ${r?renderReport(r):`<div class="card empty">${t('أضف تقرير جهاز لبدء الفحص','Add an equipment report to start inspection')}</div>`}
    <div class="footer-bar"><span>${locked()?t('النسخة الموقعة محفوظة','Signed copy saved'):t('الصور والبيانات تُحفظ على هذا الجهاز','Data and photos are saved on this device')}</span><div class="actions">
    ${!locked()?button('sign',t('التوقيع وإنهاء الزيارة','Sign & finish visit'),true):button('print',t('طباعة / حفظ PDF','Print / save PDF'))}
    ${current.state==='queued'?button('sync',t('إرسال الآن','Send now'),true):''}
    ${!locked()&&!current.existing_visit_id?button('delete-visit',t('حذف المسودة','Delete draft')):''}
    </div></div>`;
    if (step===9 && !locked()) setupSignatures();
}

function allowanceHint() {
    const c = data.contracts.find(c=>c.id===Number(current.contract_id));
    if (!c || c.limit === null) return '';
    const local = visits.filter(v=>v.contract_id===c.id&&v.kind==='emergency'&&v.state==='queued').length;
    return `<p class="muted">${t('زيارات الطوارئ المسجلة','Recorded emergency visits')}: ${c.used} / ${c.limit}${local?` · ${local} ${t('بانتظار المزامنة','awaiting sync')}`:''}. ${c.used>=c.limit?t('الرصيد مستنفد؛ الزيارة الإضافية تحتاج مراجعة رسومها.','Allowance exhausted; review charges for additional visits.'):''}</p>`;
}

function renderReport(r) {
    const steps = FORM_STEPS.map(s=>`<button data-action="step" data-id="${s.step}" class="${s.step===step?'active':''}">${s.step}. ${escape(t(s.titleAr,s.title))}</button>`).join('');
    let content = '';
    if (step===1) content = `<div class="grid">${input(t('التاريخ','Date'),'date',r.date,'date')}<div><label>${t('نوع الصيانة','Maintenance type')}</label><select data-key="maintenance_type">${options(MAINTENANCE_TYPES.map(x=>({id:x,name:maintenanceLabel(x)})),r.maintenance_type,false)}</select></div>${input(t('الموقع','Site'),'site',r.site)}${input(t('اسم الجهاز','Unit name'),'unit_name',r.unit_name)}</div>`;
    if (step===2) content = `<div class="grid"><div><label>${t('الجهاز المسجّل','Installed unit')}</label><select data-key="equipment_unit_id">${options(availableUnits(),r.equipment_unit_id)}</select></div>
    <div><label>${t('نموذج التطبيق','App template')}</label><select data-key="machine_type">${options(MACHINE_TYPES.map(m=>({id:m.id,name:t(m.labelAr,m.labelEn)})),r.machine_type,false)}</select></div>
    <div><label>${t('نموذج من النظام','System template')}</label><select data-key="template_id">${options(data.templates,r.template_id)}</select></div>
    ${input(t('الموديل','Model'),'model',r.model)}${input(t('الرقم التسلسلي','Serial number'),'serial',r.serial)}${input(t('الشركة المصنّعة','Manufacturer'),'brand',r.brand)}${input(t('السعة','Capacity'),'capacity',r.capacity)}${input(t('سنة التركيب','Installation year'),'installation_year',r.installation_year)}
    <div><label>${t('وحدة الحرارة','Temperature unit')}</label><select data-key="temp_unit">${options(['C','F','K'].map(x=>({id:x,name:x})),r.temp_unit,false)}</select></div><div><label>${t('وحدة الضغط','Pressure unit')}</label><select data-key="pressure_unit">${options(['bar','psi','kPa','MPa'].map(x=>({id:x,name:x})),r.pressure_unit,false)}</select></div></div>
    <p>${button('unit-documents',t('مانيوالات الجهاز','Unit manuals'))} ${button('save-template',t('حفظ إعدادات هذا الجهاز','Save this unit setup'))}</p>`;
    if (step===3) content = r.fields.filter(f=>!['attention','materials','machine'].includes(f.section)).map(f=>renderField(f,r.fields.indexOf(f))).join('')+`<p>${button('custom-field',t('+ بند فحص / قراءة','+ Checklist item / reading'))}</p>`;
    if (step===4) content = `<div class="grid">${textarea(t('المشكلة','Problem'),'problem',r.problem)}${textarea(t('الصيانة المطلوبة / العمل المنفذ','Required maintenance / work performed'),'solution',r.solution)}</div>`;
    if (step===5) content = r.fields.filter(f=>f.section==='attention').map(f=>renderField(f,r.fields.indexOf(f))).join('')+`<p>${button('attention',t('+ ملاحظة بحاجة انتباه','+ Attention note'))}</p>`;
    if (step===6) content = `<p class="muted">${t('سجل وصف المادة والكمية والوحدة المستخدمة.','Record the material description, quantity and unit used.')}</p>`+r.fields.filter(f=>f.section==='materials').map(f=>renderField(f,r.fields.indexOf(f))).join('')+`<p>${button('material',t('+ مادة','+ Material'))}</p>`;
    if (step===7) content = textarea(t('ملاحظات عامة','General notes'),'notes',r.notes);
    if (step===8) content = `<label>${t('التقاط أو إضافة صور','Take or add photos')}<input type="file" id="photos" accept="image/jpeg,image/png" multiple></label><div class="photo-grid">${r.photos.map((p,i)=>`<div><img src="${escape(p.image)}" alt="${escape(p.caption)}"><input data-caption="${i}" value="${escape(p.caption)}" placeholder="${t('وصف الصورة','Photo caption')}">${!locked()?button('remove-photo',t('إزالة','Remove'),false,`data-id="${i}"`):''}</div>`).join('')}</div>`;
    if (step===9) content = `<p>${t('التوقيع يشمل جميع تقارير الأجهزة في هذه الزيارة. بعد الإنهاء تُقفل النسخة الموقعة وتُرسل للمراجعة عند توفر الإنترنت.','Signatures cover all equipment reports in this visit. Finishing locks the signed copy and sends it for review when online.')}</p><div class="grid">${['technician','customer'].map(key=>`<div><label>${key==='technician'?t('توقيع الفني','Technician signature'):t('توقيع العميل','Customer signature')}</label><input data-signature-name="${key}" value="${escape(current[key+'_signature']?.name || (key==='technician'?config.user.name:''))}" placeholder="${t('اسم الموقّع','Signer name')}">${locked()?`<img class="signature-preview" src="${escape(current[key+'_signature']?.image)}" alt="signature">`:`<canvas class="signature" data-signature="${key}"></canvas>${button('clear-signature',t('مسح التوقيع','Clear signature'),false,`data-id="${key}"`)}`}</div>`).join('')}</div>`;
    const receipt = current.receipt?.reports?.find(x=>x.uuid===r.uuid);
    return `<div class="steps">${steps}</div><section class="card"><div class="row"><h2>${escape(r.title)}</h2>${receipt?`<a class="button" href="${escape(receipt.pdf_url)}" target="_blank">PDF</a>`:''}</div><fieldset ${locked()?'disabled':''}>${content}</fieldset></section><div class="actions">${step>1?button('step',t('السابق','Previous'),false,`data-id="${step-1}"`):''}${step<9?button('step',t('التالي','Next'),true,`data-id="${step+1}"`):''}</div>`;
}

function renderField(f, index) {
    const unit = f.unit === 'temperature' ? report().temp_unit : f.unit === 'pressure' ? report().pressure_unit : f.unit;
    let control = `<input data-field="${index}" value="${escape(f.answer)}" ${f.type==='number'?'inputmode="decimal"':''}>`;
    if (f.options?.length || f.type==='boolean') control = `<select data-field="${index}">${options((f.options?.length?f.options:['0','1']).map(x=>({id:x,name:x})),f.answer)}</select>`;
    if (f.type==='images') control = `<small>${t('الصور الموجودة محفوظة في التقرير. أضف صوراً جديدة من خطوة الصور.','Existing images remain in the report. Add new ones in Photos.')}</small>`;
    return `<div class="field-row"><label>${escape(t(f.question,f.question_en||f.question))}${unit?` (${escape(unit)})`:''}<small>${escape(t(f.question_en,f.question))}</small></label>${control}${!locked()&&f.type!=='images'?`<button type="button" class="icon-button" data-action="remove-field" data-id="${index}" title="${t('إزالة البند','Remove field')}">×</button>`:''}</div>`;
}

function availableUnits() {
    const c = data.contracts.find(c=>c.id===Number(current.contract_id));
    const s = data.schedules.find(s=>s.id===current.scheduled_visit_id);
    return data.units.filter(u=>u.customer_id===Number(current.customer_id) && (!c||c.unit_ids.includes(u.id)) && (!s?.unit_id||u.id===s.unit_id));
}

function templateFields(type, values = {}) {
    const setup = unitConfigFromValues(type, values);
    const defaults = unitConfigValues(setup);
    return buildChecklist(type,setup).map(f=>({
        field_key:f.key,question:f.labelAr||f.labelEn,question_en:f.labelEn,
        type:['dropdown','count'].includes(f.type)?'choice':['number','temperature','pressure'].includes(f.type)?'number':'text',
        options:f.options||[],answer:values[f.key]??defaults[f.key]??'',section:f.section||'checklist',
        unit:['temperature','pressure'].includes(f.type)?f.type:f.unit,
    }));
}

async function addReport() {
    if (!current.customer_id) return notice(t('اختر العميل أولاً.','Select a customer first.'),true);
    const unit = availableUnits()[0];
    const r = {uuid:uuid(),title:t('تقرير جهاز','Equipment report'),machine_type:'custom',maintenance_type:current.kind==='emergency'?'Emergency':'Preventive',
        date:today(),equipment_unit_id:unit?.id||null,unit_name:unit?.name||'',model:unit?.model||'',serial:unit?.serial||'',site:unit?.site||'',brand:unit?.manufacturer||'',
        fields:[],photos:[],problem:'',solution:'',notes:'',temp_unit:'C',pressure_unit:'bar'};
    if (unit) Object.assign(r,await read(db,'preferences','unit-'+unit.id)||{});
    current.reports.push(r);reportIndex=current.reports.length-1;step=2;await localSave();render();
}

function renderReports() {
    main.innerHTML = `<div class="page-title"><h1>${t('التقارير المحفوظة','Saved reports')}</h1></div><input id="report-search" class="search" placeholder="${t('بحث بالعميل أو الموديل أو التاريخ','Search customer, model or date')}"><div id="report-results" class="report-grid" style="margin-top:20px"></div>`;
    filterReports('');
}
function filterReports(query) {
    document.getElementById('report-results').innerHTML=visits.filter(v=>(customerName(v.customer_id)+' '+v.check_in_at+' '+v.reports.map(r=>r.title+' '+r.model+' '+r.serial).join(' ')).toLowerCase().includes(query.toLowerCase())).map(visitCard).join('');
}

function renderEquipment() {
    main.innerHTML=`<div class="page-title"><h1>${t('الأجهزة لدى العملاء','Installed equipment')}</h1></div><input class="search" id="unit-search" placeholder="${t('بحث بالاسم أو التسلسلي أو العميل','Search name, serial or customer')}"><div class="report-grid" id="unit-results" style="margin-top:20px"></div>`;
    filterUnits('');
}
function filterUnits(query) {
    document.getElementById('unit-results').innerHTML=data.units.filter(u=>(u.name+' '+u.model+' '+u.serial+' '+customerName(u.customer_id)).toLowerCase().includes(query.toLowerCase())).slice(0,150).map(u=>`<div class="card"><span class="badge">${escape(u.category)}</span><h3>${escape(u.name)}</h3><p>${escape(customerName(u.customer_id))}<br>${escape(u.model)} · ${escape(u.serial)}<br>${escape(u.site)}</p>${button('equipment-docs',t('المانيوالات','Manuals'),false,`data-id="${u.id}"`)}</div>`).join('');
}

let documentUnit = null;
function reaches(document,unit) {
    if (!unit) return true;
    const assignments=document.assignments||(document.assignment?[document.assignment]:[]);
    const fields={unit:['equipment_unit_id','id'],model:['equipment_model_id','model_id'],category:['equipment_category_id','category_id'],subtype:['equipment_subtype_id','subtype_id'],manufacturer:['equipment_manufacturer_id','manufacturer_id']};
    return assignments.some(a=>a.level==='all'||(fields[a.level]&&a[fields[a.level][0]]&&a[fields[a.level][0]]===unit[fields[a.level][1]]));
}
async function renderDocuments() {
    const unit=data.units.find(u=>u.id===documentUnit);
    const stored=await all(db,'documents');
    if(tab!=='documents'||current)return;
    main.innerHTML=`<div class="page-title"><div><h1>${t('المانيوالات والكتالوجات','Manuals & catalogues')}</h1><span class="muted">${escape(unit?.name||t('جميع الوثائق','All documents'))}</span></div>${button('all-documents',t('عرض الكل','Show all'))}</div><p class="muted">${t('نزّل الملفات المطلوبة أثناء الاتصال؛ علامة «محفوظ» تعني أنها متاحة بدون إنترنت.','Download the needed files while online. “Saved” means available offline.')}</p><div class="report-grid">${data.documents.filter(d=>reaches(d,unit)).map(d=>{
        const copy=stored.find(s=>s.id===d.id);const currentCopy=copy?.version===d.version;
        return `<div class="card"><h3>${escape(d.name)}</h3><p class="muted">${escape(d.filename)} · ${Math.round(d.size/1024)} KB</p><span class="badge ${currentCopy?'green':''}">${copy?(currentCopy?t('محفوظ','Saved'):t('يوجد تحديث','Update available')):t('غير منزّل','Not downloaded')}</span><p class="actions">${button('download-document',copy?t('تحديث','Update'):t('تنزيل للأوفلاين','Download offline'),false,`data-id="${d.id}"`)}${copy?button('open-document',t('فتح','Open'),false,`data-id="${d.id}"`):''}</p></div>`;}).join('')}</div>`;
}
async function downloadDocument(id) {
    const doc=data.documents.find(d=>d.id===id);
    const response=await fetch(doc.url,{credentials:'same-origin',cache:'no-store',headers:{Accept:'application/pdf,application/octet-stream'}});
    if (!response.ok||response.redirected||response.headers.get('Content-Type')?.includes('text/html')) throw new Error(t('تعذّر تنزيل الملف. تأكد من الاتصال وتسجيل الدخول.','Download failed. Check your connection and sign-in.'));
    const saved = {id,version:doc.version,filename:doc.filename,blob:await response.blob(),pages:[]};
    await write(db,'documents',id,saved);
    try {
        const extracted=await api('/documents/'+id+'/text');saved.pages=extracted.pages;await write(db,'documents',id,saved);
    } catch(error) { notice(t('تم حفظ الملف؛ تعذّر تجهيز نصه للبحث. يمكن فتحه بدون إنترنت.','File saved; text indexing was unavailable. You can open it offline.')); }
    notice(t('تم حفظ الملف على الجهاز.','File saved on this device.'));if(tab==='documents'&&!current)renderDocuments();
}

async function renderAlarms() {
    lookup=lookup||await read(db,'meta','alarms');
    if(tab!=='alarms'||current)return;
    main.innerHTML=`<div class="page-title"><div><h1>${t('الأكواد والبارامترات','Codes & parameters')}</h1><span class="muted">${lookup?lookup.alarms.length+lookup.database.length:0} ${t('سجلات محفوظة','saved entries')}</span></div>${button('download-alarms',t('تحديث القائمة','Update list'))}</div><p class="muted">${t('البحث في بيانات التطبيق والمانيوالات المحفوظة؛ لا يتم توليد إجابات.','Search the supplied app data and saved manual text; answers are not generated.')}</p><input id="alarm-search" class="search" placeholder="${t('كود إنذار أو رقم بارامتر أو كلمة','Alarm code, parameter number or keyword')}"><div id="alarm-results" style="margin-top:20px"></div>`;
}
async function searchAlarms(query) {
    if (query.trim().length<2) {document.getElementById('alarm-results').innerHTML='';return;}
    const needle=query.trim().toLowerCase(),key=needle.replace(/[^a-z0-9]/g,'');
    const rows=lookup?[...lookup.database,...lookup.alarms.map(row=>Object.fromEntries(lookup.fields.map((f,i)=>[f,row[i]])))]:[];
    const result=rows.filter(row=>(key&&String(row.key).toLowerCase()===key)||Object.values(row).some(v=>String(v||'').toLowerCase().includes(needle))).slice(0,40);
    document.getElementById('alarm-results').innerHTML=result.map(row=>`<article class="card"><span class="badge">${escape(row.controller)}</span><h3>${escape(row.code)} · ${escape(t(row.name_ar,row.name_en))}</h3>${[['causes','الأسباب','Causes'],['checks','الفحص','Checks'],['effect','الأثر','Effect'],['action','الإجراء','Action'],['reset','إعادة الضبط','Reset']].map(([field,ar,en])=>`<details><summary>${t(ar,en)}</summary><p class="lookup-text">${escape(t(row[field+'_ar']||row[field+'_en'],row[field+'_en']))}</p></details>`).join('')}${row.source_page?`<small>${t('صفحة المصدر','Source page')}: ${escape(row.source_page)}</small>`:''}</article>`).join('')||`<p class="empty">${t('لا توجد نتيجة في البيانات المحفوظة. افتح المانيوال وابحث داخله.','No match in saved data. Open the manual and search within it.')}</p>`;
    const documents=await all(db,'documents');
    if(document.getElementById('alarm-search')?.value!==query)return;
    const pages=documents.flatMap(d=>(d.pages||[]).filter(p=>p.text.toLowerCase().includes(needle)).map(p=>({document:d,page:p}))).slice(0,30);
    document.getElementById('alarm-results').insertAdjacentHTML('beforeend',pages.map(({document:d,page:p})=>{
        const at=p.text.toLowerCase().indexOf(needle);
        return `<article class="card"><h3>${escape(d.filename)} · ${t('صفحة','Page')} ${p.number}</h3><p class="lookup-text">${escape(p.text.slice(Math.max(0,at-180),at+900))}</p>${button('open-document',t('فتح المانيوال','Open manual'),false,`data-id="${d.id}"`)}</article>`;
    }).join(''));
}

function renderSettings() {
    main.innerHTML=`<h1>${t('إعدادات الجهاز','Device settings')}</h1><section class="card"><h2>${escape(config.user.name)}</h2><p>${t('هذا الجهاز يحتفظ بالمسودات والصور والتوقيعات لحسابك. افتح الواجهة أثناء الاتصال لتحديث الأجهزة والزيارات، ثم نزّل المانيوالات التي تحتاجها.','This device keeps drafts, photos and signatures for your account. Open the portal online to update equipment and visits, then download the manuals you need.')}</p><div class="actions">${button('sync',t('تحديث ومزامنة','Refresh & sync'),true)}${button('download-alarms',t('تنزيل قائمة الأكواد','Download code list'))}${button('export-backup',t('تصدير نسخة من التقارير','Export report backup'))}${button('logout',t('تسجيل الخروج','Sign out'))}</div></section>`;
}

function setupSignatures() {
    document.querySelectorAll('canvas[data-signature]').forEach(canvas=>{
        const key=canvas.dataset.signature,ratio=devicePixelRatio||1;
        canvas.width=canvas.clientWidth*ratio;canvas.height=canvas.clientHeight*ratio;
        const ctx=canvas.getContext('2d');ctx.scale(ratio,ratio);ctx.lineWidth=2;ctx.lineCap='round';ctx.strokeStyle='#153443';
        if(current[key+'_signature']?.image){const img=new Image();img.onload=()=>ctx.drawImage(img,0,0,canvas.clientWidth,canvas.clientHeight);img.src=current[key+'_signature'].image;}
        let drawing=false,moved=false;
        const point=e=>{const box=canvas.getBoundingClientRect();return [e.clientX-box.left,e.clientY-box.top];};
        canvas.addEventListener('pointerdown',e=>{drawing=true;moved=false;canvas.setPointerCapture(e.pointerId);ctx.beginPath();ctx.moveTo(...point(e));});
        canvas.addEventListener('pointermove',e=>{if(drawing){ctx.lineTo(...point(e));ctx.stroke();moved=true;}});
        const end=()=>{if(!drawing)return;drawing=false;if(!moved)return;const name=document.querySelector(`[data-signature-name="${key}"]`).value;current[key+'_signature']={image:canvas.toDataURL('image/png'),name,signed_at:now()};localSave();};
        canvas.addEventListener('pointerup',end);canvas.addEventListener('pointercancel',end);
    });
}

async function photoData(file) {
    const img=await createImageBitmap(file),scale=Math.min(1,1200/Math.max(img.width,img.height));
    const canvas=document.createElement('canvas');canvas.width=Math.round(img.width*scale);canvas.height=Math.round(img.height*scale);
    canvas.getContext('2d').drawImage(img,0,0,canvas.width,canvas.height);img.close();
    return canvas.toDataURL('image/jpeg',.72);
}

function payloadFor(visit) {
    const payload={uuid:visit.uuid,user_id:config.user.id,existing_visit_id:visit.existing_visit_id||null,
        customer_id:Number(visit.customer_id),contract_id:visit.contract_id||null,scheduled_visit_id:visit.scheduled_visit_id||null,
        maintenance_request_id:visit.maintenance_request_id||null,kind:visit.kind,check_in_at:visit.check_in_at,finished_at:now(),
        technician_signature:visit.technician_signature,customer_signature:visit.customer_signature,
        reports:visit.reports.map(r=>{
            const fields=r.fields.map(f=>({id:f.id||null,field_key:f.field_key||null,question:f.question,question_en:f.question_en||null,type:f.type,options:f.options||[],answer:f.answer===''?null:String(f.answer??''),section:f.section||null}));
            for(const [key,ar,en] of [['brand','الشركة المصنّعة','Manufacturer'],['capacity','السعة','Capacity'],['installation_year','سنة التركيب','Installation year'],['temp_unit','وحدة الحرارة','Temperature unit'],['pressure_unit','وحدة الضغط','Pressure unit']]) if(r[key]) fields.push({field_key:key,question:ar,question_en:en,type:'text',answer:r[key],section:'machine'});
            return {uuid:r.uuid,existing_report_id:r.existing_report_id||null,base_updated_at:r.base_updated_at||null,template_id:r.template_id||null,equipment_unit_id:r.equipment_unit_id||null,
                title:r.title,date:r.date,maintenance_type:r.maintenance_type,problem:r.problem||'',solution:r.solution||'',notes:r.notes||'',unit_name:r.unit_name||'',model:r.model||'',serial:r.serial||'',site:r.site||'',fields,photos:r.photos};
        })};
    return payload;
}

async function finish() {
    if (locked()) return;
    if (!current.customer_id||!current.reports.length) throw new Error(t('اختر العميل وأضف تقرير جهاز.','Choose a customer and add an equipment report.'));
    if (current.reports.some(r=>r.content_status&&r.content_status!=='received')) throw new Error(t('أحد التقارير دخل المراجعة. حدّث الزيارة قبل الإرسال.','A report is already under review. Refresh the visit before submitting.'));
    if (!current.technician_signature?.image||!current.customer_signature?.image||!current.technician_signature?.name?.trim()||!current.customer_signature?.name?.trim()) {
        step=9;render();return notice(t('يلزم اسم وتوقيع الفني والعميل.','Technician and customer names and signatures are required.'),true);
    }
    await localSave();if(saveError)throw saveError;
    const payload=payloadFor(current);
    if(new Blob([JSON.stringify(payload)]).size>7*1024*1024)throw new Error(t('حجم الزيارة كبير. قلّل عدد الصور قبل التوقيع النهائي.','This visit is too large. Reduce the photos before final signing.'));
    if(!confirm(t('إنهاء الزيارة وقفل نسختها الموقعة؟','Finish the visit and lock its signed copy?')))return;
    const saved={...current,state:'queued',payload};
    await write(db,'visits',saved.uuid,saved);current=saved;visits=await all(db,'visits');render();synchronize();
}

function printVisit() {
    const popup=window.open('','_blank');if(!popup)return notice(t('اسمح بفتح نافذة الطباعة.','Allow the print window to open.'),true);
    const html=current.reports.map(r=>`<section><h1>ASEE · ${escape(r.title)}</h1><p>${escape(customerName(current.customer_id))} · ${escape(r.date)} · ${escape(r.model)} · ${escape(r.serial)}</p><table>${r.fields.filter(f=>f.type!=='images').map(f=>`<tr><td>${escape(f.question_en)} / ${escape(f.question)}</td><td>${escape(f.answer)}</td></tr>`).join('')}</table><h3>${t('العمل المنفذ والملاحظات','Work & notes')}</h3><p>${escape(r.solution)}</p><p>${escape(r.notes)}</p>${r.photos.map(p=>`<p><img style="max-width:90%;max-height:450px" src="${escape(p.image)}"><br>${escape(p.caption)}</p>`).join('')}<h3>Signatures / التوقيعات</h3>${['technician','customer'].map(k=>`<div>${escape(current[k+'_signature']?.name)}<br><img style="height:80px" src="${escape(current[k+'_signature']?.image)}"></div>`).join('')}</section>`).join('');
    popup.document.write(`<!doctype html><html dir="${locale==='ar'?'rtl':'ltr'}"><head><meta charset="utf-8"><title>ASEE signed report</title><style>body{font:12px Tahoma,Arial;padding:25px;color:#17303b}section{break-after:page}table{border-collapse:collapse;width:100%}td{border:1px solid #ccc;padding:6px}h1{color:#0d766e}p{white-space:pre-wrap}</style></head><body>${html}</body></html>`);popup.document.close();popup.onload=()=>popup.print();
}

document.addEventListener('click',async event=>{
    const target=event.target.closest('[data-action],[data-tab]');if(!target)return;
    try {
        if(target.dataset.tab){if(current)await localSave();current=null;tab=target.dataset.tab;visits=await all(db,'visits');render();window.scrollTo(0,0);return;}
        const action=target.dataset.action,id=target.dataset.id;
        if(action==='sync')return synchronize();
        if(action==='new')return newVisit();
        if(action==='scheduled')return newVisit(data.schedules.find(s=>s.id===Number(id)));
        if(action==='open'){current=await read(db,'visits',id);reportIndex=0;step=1;render();}
        if(action==='back'){await localSave();current=null;visits=await all(db,'visits');render();}
        if(action==='report'){reportIndex=Number(id);step=1;render();}
        if(action==='step'){step=Number(id);render();}
        if(action==='add-report')await addReport();
        if(action==='remove-field'){report().fields.splice(Number(id),1);await localSave();render();}
        if(action==='custom-field'){const label=prompt(t('اسم بند الفحص / القراءة','Checklist item / reading label'));if(label){report().fields.push({field_key:'custom_'+uuid(),question:label,question_en:label,type:'text',answer:'',section:'checklist'});await localSave();render();}}
        if(action==='attention'){report().fields.push({field_key:'attention_'+uuid(),question:'الجهاز / ملاحظة بحاجة انتباه',question_en:'Unit / attention note',type:'text',answer:'',section:'attention'});await localSave();render();}
        if(action==='material'){const key=uuid();for(const [suffix,ar,en] of [['description','وصف المادة','Material description'],['quantity','الكمية','Quantity'],['unit','الوحدة','Unit']])report().fields.push({field_key:`material_${key}_${suffix}`,question:ar,question_en:en,type:suffix==='quantity'?'number':'text',answer:'',section:'materials'});await localSave();render();}
        if(action==='remove-photo'){report().photos.splice(Number(id),1);await localSave();render();}
        if(action==='clear-signature'){delete current[id+'_signature'];await localSave();render();}
        if(action==='sign'){if(step!==9){step=9;render();}else await finish();}
        if(action==='print')printVisit();
        if(action==='delete-visit'&&!locked()&&!current.existing_visit_id&&confirm(t('حذف هذه المسودة من الجهاز؟','Delete this draft from the device?'))){await remove(db,'visits',current.uuid);current=null;visits=await all(db,'visits');render();}
        if(action==='equipment-docs'||action==='unit-documents'){documentUnit=action==='unit-documents'?Number(report().equipment_unit_id):Number(id);if(current)await localSave();current=null;tab='documents';render();}
        if(action==='all-documents'){documentUnit=null;renderDocuments();}
        if(action==='download-document')await downloadDocument(Number(id));
        if(action==='open-document'){const doc=await read(db,'documents',Number(id));const url=URL.createObjectURL(doc.blob);const link=document.createElement('a');link.href=url;link.target='_blank';link.rel='noopener';link.click();setTimeout(()=>URL.revokeObjectURL(url),60000);}
        if(action==='download-alarms'){lookup=await api('/alarms-data');await write(db,'meta','alarms',lookup);notice(t('تم تنزيل قائمة الأكواد.','Code list downloaded.'));if(tab==='alarms')renderAlarms();}
        if(action==='save-template'&&!locked()){const r=report();if(!r.equipment_unit_id)throw new Error(t('اختر الجهاز أولاً.','Select a unit first.'));await write(db,'preferences','unit-'+r.equipment_unit_id,{machine_type:r.machine_type,title:r.title,temp_unit:r.temp_unit,pressure_unit:r.pressure_unit,fields:r.fields.map(f=>({...f,answer:f.section==='config'?f.answer:''}))});notice(t('تم حفظ إعدادات الجهاز لهذا الحساب.','Unit setup saved for this account.'));}
        if(action==='export-backup'){const link=document.createElement('a');link.href=URL.createObjectURL(new Blob([JSON.stringify(await all(db,'visits'))],{type:'application/json'}));link.download=`asee-reports-${today()}.json`;link.click();setTimeout(()=>URL.revokeObjectURL(link.href),1000);}
        if(action==='logout'){if(current)await localSave();navigator.serviceWorker?.controller?.postMessage({type:'LOGOUT'});const form=document.createElement('form');form.method='POST';form.action=config.logout;const token=document.createElement('input');token.type='hidden';token.name='_token';token.value=config.csrf;form.append(token);document.body.append(form);form.submit();}
        if(['step','report','open','back','add-report','sign'].includes(action))window.scrollTo(0,0);
    } catch(error){notice(error.message,true);}
});

main.addEventListener('input',event=>{
    const el=event.target;
    if(el.id==='report-search')return filterReports(el.value);
    if(el.id==='unit-search')return filterUnits(el.value);
    if(el.id==='alarm-search')return searchAlarms(el.value);
    if(!current||locked())return;
    if(el.dataset.key&&el.tagName!=='SELECT')report()[el.dataset.key]=el.value;
    if(el.dataset.field!==undefined&&el.tagName!=='SELECT')report().fields[Number(el.dataset.field)].answer=el.value;
    if(el.dataset.caption!==undefined)report().photos[Number(el.dataset.caption)].caption=el.value;
    if(el.dataset.signatureName&&current[el.dataset.signatureName+'_signature'])current[el.dataset.signatureName+'_signature'].name=el.value;
    localSave();
});
main.addEventListener('change',async event=>{
    if(!current||locked())return;
    const el=event.target,r=report();
    try {
        if(el.dataset.visit){
            if(el.dataset.visit==='customer_id'&&current.reports.length){render();throw new Error(t('ابدأ زيارة جديدة لتغيير العميل بعد إضافة تقارير.','Start a new visit to change customer after adding reports.'));}
            current[el.dataset.visit]=el.dataset.visit==='kind'?el.value:(Number(el.value)||null);
            if(el.dataset.visit==='customer_id'){current.contract_id=null;current.maintenance_request_id=null;}
            render();
        }
        if(el.dataset.key&&el.tagName==='SELECT'){
            const key=el.dataset.key;
            if(['machine_type','template_id'].includes(key)&&r.fields.some(f=>f.answer&&f.section!=='config')&&!confirm(t('تغيير النموذج يعيد إنشاء بنود الفحص. هل تريد المتابعة؟','Changing the template rebuilds checklist rows. Continue?'))){render();return;}
            r[key]=['template_id','equipment_unit_id'].includes(key)?Number(el.value)||null:el.value;
            if(key==='equipment_unit_id'){const unit=data.units.find(u=>u.id===r[key]);if(unit)Object.assign(r,{unit_name:unit.name,model:unit.model,serial:unit.serial,site:unit.site,brand:unit.manufacturer});}
            if(key==='machine_type'){r.template_id=null;r.fields=templateFields(r.machine_type);r.title=t(MACHINE_TYPES.find(m=>m.id===r.machine_type).labelAr,MACHINE_TYPES.find(m=>m.id===r.machine_type).labelEn);}
            if(key==='template_id'&&r.template_id){const template=data.templates.find(x=>x.id===r.template_id);r.fields=clone(template.fields).filter(f=>f.type!=='images');r.title=template.name;r.machine_type='custom';}
            render();
        }
        if(el.dataset.field!==undefined&&el.tagName==='SELECT'){
            const field=r.fields[Number(el.dataset.field)];field.answer=el.value;
            if(field.section==='config'&&r.machine_type!=='custom'){
                const values=Object.fromEntries(r.fields.map(f=>[f.field_key,f.answer]));
                const custom=r.fields.filter(f=>f.field_key?.startsWith('custom_')||['attention','materials'].includes(f.section));
                r.fields=[...templateFields(r.machine_type,values),...custom];render();
            }
        }
        if(el.id==='photos'){
            if(r.photos.length+el.files.length>12)throw new Error(t('الحد 12 صورة لكل تقرير.','Limit: 12 photos per report.'));
            for(const file of el.files)r.photos.push({image:await photoData(file),caption:''});render();
        }
        await localSave();
    }catch(error){notice(error.message,true);}
});

document.getElementById('language').addEventListener('click',()=>{locale=locale==='ar'?'en':'ar';document.documentElement.dir=locale==='ar'?'rtl':'ltr';document.documentElement.lang=locale;render();});
window.addEventListener('online',()=>{connection();synchronize();});window.addEventListener('offline',connection);
setInterval(()=>{if(document.visibilityState==='visible')synchronize();},60000);

async function start() {
    db=await openWorkspace(config.user.id);data=await read(db,'meta','data');visits=await all(db,'visits');render();
    if('serviceWorker' in navigator&&window.isSecureContext){
        navigator.serviceWorker.register(config.base+'/worker.js',{scope:config.base+'/'}).then(()=>navigator.serviceWorker.ready).then(reg=>reg.active?.postMessage({type:'PREPARE'})).catch(error=>notice(error.message,true));
    }else notice(t('فتح الواجهة بدون إنترنت يحتاج HTTPS أو localhost. الحفظ المحلي متاح في المتصفح الحالي.','Offline page opening requires HTTPS or localhost. Local saving is available in this browser.'));
    if(navigator.onLine)await synchronize();
    if(config.initialVisitId){const visit=(await all(db,'visits')).find(v=>v.existing_visit_id===config.initialVisitId);if(visit){current=visit;render();}}
}
start().catch(error=>notice(error.message,true));
