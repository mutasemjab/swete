// Source: user-supplied hvac-maintenance-app-2.zip / constants/company.ts
export const COMPANY = {
    nameEn: 'Aladdin Sweiti Engineering Est.',
    nameAr: 'مؤسسة علاء الدين السويطي الهندسية',
    footerEn: 'Jordan- Amman- Princess Tharwat st.- 75 Joanna Center- Office 102\nTel +(962) (6) 585 5959   Fax +(962) (6) 581 4455\nE-mail  info@asee-eng.com   www.asee-eng.com',
    footerAr: 'الأردن- عمان- شارع الأميرة ثروت- 75 مجمع جوانا- مكتب 102\nتلفون 0909 585 (6) (962)+ فاكس 4455 581 (6) (962) +\nبريد إلكتروني info@asee-eng.com   www.asee-eng.com'
};
export const MAINTENANCE_TYPES = [
    'Startup',
    'Preventive',
    'Corrective',
    'Emergency',
    'Commissioning'
];
const MAINTENANCE_AR = {
    Startup: 'تشغيل أولي',
    Preventive: 'صيانة وقائية',
    Corrective: 'صيانة تصحيحية',
    Emergency: 'صيانة طارئة',
    Commissioning: 'تشغيل وتسليم'
};
export const maintenanceAr = (type)=>MAINTENANCE_AR[type] ?? '';
export const maintenanceLabel = (type)=>MAINTENANCE_AR[type] ? `${MAINTENANCE_AR[type]} / ${type}` : type;
export const REFRIGERANT_TYPES = [
    'R410A',
    'R32',
    'R134a',
    'R22',
    'R407C',
    'R404A',
    'Other'
];
