// Source: user-supplied hvac-maintenance-app-2.zip / constants/checklists.ts
import { buildChillerChecklist, getChillerBaseFields, getCircuitFields, getCompressorFields } from './chiller-fields.js';
import { REFRIGERANT_TYPES } from './company.js';
const OK_NOT_OK = [
    'OK',
    'Not OK'
];
const OK_NOT_OK_NA = [
    'OK',
    'Not OK',
    'Not Available'
];
const OK_REPLACED_DIRTY = [
    'OK',
    'Replaced',
    'Dirty'
];
const OK_BLOCKED = [
    'OK',
    'Blocked'
];
const OK_NA = [
    'OK',
    'Not Available'
];
const OK_NOT_OK_COIL = [
    'OK',
    'Not OK'
];
const FAN_SPEED = [
    'Low',
    'Med',
    'High'
];
const OK_REPLACED = [
    'OK',
    'Replaced'
];
const OK_LOW = [
    'OK',
    'Low'
];
const OK_FAULT = [
    'OK',
    'Fault'
];
const OK_REPLACED_NA = [
    'OK',
    'Replaced',
    'Not Available'
];
const OK_CLEANED_DIRTY = [
    'OK',
    'Cleaned',
    'Dirty'
];
const DYNAMIC = {
    key: '__dynamic__',
    labelEn: '',
    labelAr: '',
    type: 'text'
};
const CHILLER_STATIC = getChillerBaseFields().filter((f)=>!f.key.endsWith('_count')).flatMap((f)=>f.key === 'refrigerant_type' ? [
        f,
        DYNAMIC
    ] : [
        f
    ]);
const STATIC_TEMPLATES = {
    ahu: [
        {
            key: 'voltage',
            labelEn: '3-Phase Voltage (V)',
            labelAr: 'الفولتيه',
            type: 'voltage',
            placeholder: 'P1-P2: ___ P1-P3: ___ P2-P3: ___ / P1-E: ___ P2-E: ___ P3-E: ___'
        },
        {
            key: 'phase_rotation',
            labelEn: 'Phase Rotation',
            labelAr: 'اتجاه الفازات',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'electrical_panel',
            labelEn: "Electrical Panel Status",
            labelAr: 'لوحة الكهرباء',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        DYNAMIC,
        {
            key: 'strainer',
            labelEn: "Strainer's Status",
            labelAr: 'السترينر',
            type: 'dropdown',
            options: OK_NOT_OK_NA
        },
        {
            key: 'water_in_temp',
            labelEn: 'Water In Temperature',
            labelAr: 'حرارة الماء الداخل',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'water_out_temp',
            labelEn: 'Water Out Temperature',
            labelAr: 'حرارة الماء الخارج',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'air_supply_temp',
            labelEn: 'Air Temp Supply',
            labelAr: 'حرارة الهواء الداخل',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'air_return_temp',
            labelEn: 'Air Temp Return',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED_DIRTY
        },
        {
            key: 'airflow',
            labelEn: 'Airflow Supply/Return',
            labelAr: 'تدفق الهواء',
            type: 'text',
            unit: 'm³/h',
            placeholder: '___ m³/h'
        },
        {
            key: 'drain',
            labelEn: 'Drain',
            labelAr: 'الدرين',
            type: 'dropdown',
            options: OK_BLOCKED
        },
        {
            key: 'heaters',
            labelEn: 'Heaters',
            labelAr: 'الهياتر اذا وجدت',
            type: 'dropdown',
            options: OK_NA
        },
        {
            key: 'coil',
            labelEn: 'Coil',
            labelAr: 'كويل الماء',
            type: 'dropdown',
            options: OK_NOT_OK_COIL
        }
    ],
    chiller: CHILLER_STATIC,
    fcu: [
        {
            key: 'voltage',
            labelEn: 'Voltage',
            labelAr: 'الفولتيه',
            type: 'voltage'
        },
        {
            key: 'supply_air_temp',
            labelEn: 'Supply Air Temp',
            labelAr: 'حرارة الهواء المزود',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'return_air_temp',
            labelEn: 'Return Air Temp',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature',
            unit: '°C'
        },
        DYNAMIC,
        {
            key: 'fan_speed',
            labelEn: 'Fan Speed',
            labelAr: 'سرعة المروحة',
            type: 'dropdown',
            options: FAN_SPEED
        },
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED
        },
        {
            key: 'drain',
            labelEn: 'Drain',
            labelAr: 'الدرين',
            type: 'dropdown',
            options: OK_BLOCKED
        }
    ],
    split: [
        {
            key: 'voltage',
            labelEn: 'Voltage',
            labelAr: 'الفولتيه',
            type: 'voltage'
        },
        {
            key: 'supply_air_temp',
            labelEn: 'Supply Air Temp',
            labelAr: 'حرارة الهواء المزود',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'return_air_temp',
            labelEn: 'Return Air Temp',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature',
            unit: '°C'
        },
        DYNAMIC,
        {
            key: 'fan_speed',
            labelEn: 'Fan Speed',
            labelAr: 'سرعة المروحة',
            type: 'dropdown',
            options: FAN_SPEED
        },
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED
        },
        {
            key: 'drain',
            labelEn: 'Drain',
            labelAr: 'الدرين',
            type: 'dropdown',
            options: OK_BLOCKED
        }
    ],
    rtu: [
        {
            key: 'voltage',
            labelEn: '3-Phase Voltage (V)',
            labelAr: 'الفولتيه',
            type: 'voltage'
        },
        {
            key: 'phase_rotation',
            labelEn: 'Phase Rotation',
            labelAr: 'اتجاه الفازات',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'electrical_panel',
            labelEn: 'Electrical Panel Status',
            labelAr: 'لوحة الكهرباء',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'machine_load',
            labelEn: 'Machine Load (%)',
            labelAr: 'نسبة تحميل المكينة',
            type: 'number',
            unit: '%'
        },
        DYNAMIC,
        {
            key: 'condenser_fan_amps',
            labelEn: 'Condenser Fan Amps',
            labelAr: 'أمبير مروحة الكوندنسر',
            type: 'number',
            unit: 'A',
            perPhase: true
        },
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'supply_air_temp',
            labelEn: 'Supply Air Temp',
            labelAr: 'حرارة الهواء المزود',
            type: 'temperature'
        },
        {
            key: 'return_air_temp',
            labelEn: 'Return Air Temp',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature'
        },
        {
            key: 'mixed_air_temp',
            labelEn: 'Mixed Air Temp',
            labelAr: 'حرارة الهواء المخلوط',
            type: 'temperature'
        },
        {
            key: 'fresh_air_temp',
            labelEn: 'Fresh / Outside Air Temp',
            labelAr: 'حرارة الهواء النقي (الخارجي)',
            type: 'temperature'
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED_DIRTY
        },
        {
            key: 'fan_belt',
            labelEn: 'Fan Belt',
            labelAr: 'سير المروحة',
            type: 'dropdown',
            options: OK_REPLACED_NA
        },
        {
            key: 'evaporator_coil',
            labelEn: 'Evaporator Coil',
            labelAr: 'كويل المبخر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'condenser_coil',
            labelEn: 'Condenser Coil',
            labelAr: 'كويل الكوندنسر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'drain',
            labelEn: 'Drain',
            labelAr: 'الدرين',
            type: 'dropdown',
            options: OK_BLOCKED
        },
        {
            key: 'heaters',
            labelEn: 'Heaters',
            labelAr: 'الهياتر اذا وجدت',
            type: 'dropdown',
            options: OK_NA
        },
        {
            key: 'thermostat',
            labelEn: 'Thermostat / Controls',
            labelAr: 'الثرموستات والتحكم',
            type: 'dropdown',
            options: OK_NOT_OK
        }
    ],
    dehumidifier: [
        {
            key: 'voltage',
            labelEn: '3-Phase Voltage (V)',
            labelAr: 'الفولتيه',
            type: 'voltage'
        },
        {
            key: 'phase_rotation',
            labelEn: 'Phase Rotation',
            labelAr: 'اتجاه الفازات',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'electrical_panel',
            labelEn: 'Electrical Panel Status',
            labelAr: 'لوحة الكهرباء',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'machine_load',
            labelEn: 'Machine Load (%)',
            labelAr: 'نسبة تحميل المكينة',
            type: 'number',
            unit: '%'
        },
        DYNAMIC,
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'supply_air_temp',
            labelEn: 'Supply Air Temp',
            labelAr: 'حرارة الهواء المزود',
            type: 'temperature'
        },
        {
            key: 'return_air_temp',
            labelEn: 'Return Air Temp',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature'
        },
        {
            key: 'return_humidity',
            labelEn: 'Return Air Humidity',
            labelAr: 'رطوبة الهواء الراجع',
            type: 'number',
            unit: '%RH'
        },
        {
            key: 'supply_humidity',
            labelEn: 'Supply Air Humidity',
            labelAr: 'رطوبة الهواء المزود',
            type: 'number',
            unit: '%RH'
        },
        {
            key: 'humidity_setpoint',
            labelEn: 'Humidity Set Point',
            labelAr: 'ضبط الرطوبة',
            type: 'number',
            unit: '%RH'
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED_DIRTY
        },
        {
            key: 'evaporator_coil',
            labelEn: 'Evaporator Coil',
            labelAr: 'كويل المبخر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'condenser_coil',
            labelEn: 'Condenser Coil',
            labelAr: 'كويل الكوندنسر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'drain',
            labelEn: 'Drain',
            labelAr: 'الدرين',
            type: 'dropdown',
            options: OK_BLOCKED
        },
        {
            key: 'heaters',
            labelEn: 'Reheat / Heaters',
            labelAr: 'الهياتر اذا وجدت',
            type: 'dropdown',
            options: OK_NA
        },
        {
            key: 'humidistat',
            labelEn: 'Humidistat / Controls',
            labelAr: 'حساس الرطوبة والتحكم',
            type: 'dropdown',
            options: OK_NOT_OK
        }
    ],
    ccu: [
        {
            key: 'voltage',
            labelEn: '3-Phase Voltage (V)',
            labelAr: 'الفولتيه',
            type: 'voltage'
        },
        {
            key: 'phase_rotation',
            labelEn: 'Phase Rotation',
            labelAr: 'اتجاه الفازات',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'electrical_panel',
            labelEn: 'Electrical Panel Status',
            labelAr: 'لوحة الكهرباء',
            type: 'dropdown',
            options: OK_NOT_OK
        },
        {
            key: 'machine_load',
            labelEn: 'Machine Load (%)',
            labelAr: 'نسبة تحميل المكينة',
            type: 'number',
            unit: '%'
        },
        DYNAMIC,
        {
            key: 'fan_amps',
            labelEn: 'Fan Motor Amps',
            labelAr: 'أمبير محرك المروحة',
            type: 'number',
            unit: 'A',
            perPhase: true
        },
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'supply_air_temp',
            labelEn: 'Supply Air Temp',
            labelAr: 'حرارة الهواء المزود',
            type: 'temperature'
        },
        {
            key: 'return_air_temp',
            labelEn: 'Return Air Temp',
            labelAr: 'حرارة الهواء الراجع',
            type: 'temperature'
        },
        {
            key: 'temp_setpoint',
            labelEn: 'Temperature Set Point',
            labelAr: 'ضبط الحرارة',
            type: 'temperature'
        },
        {
            key: 'return_humidity',
            labelEn: 'Return Air Humidity',
            labelAr: 'رطوبة الهواء الراجع',
            type: 'number',
            unit: '%RH'
        },
        {
            key: 'humidity_setpoint',
            labelEn: 'Humidity Set Point',
            labelAr: 'ضبط الرطوبة',
            type: 'number',
            unit: '%RH'
        },
        {
            key: 'filter',
            labelEn: 'Filter Status',
            labelAr: 'الفالتر',
            type: 'dropdown',
            options: OK_REPLACED_DIRTY
        },
        {
            key: 'fan_belt',
            labelEn: 'Fan Belt',
            labelAr: 'سير المروحة',
            type: 'dropdown',
            options: OK_REPLACED_NA
        },
        {
            key: 'evaporator_coil',
            labelEn: 'Evaporator Coil',
            labelAr: 'كويل المبخر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'condenser_coil',
            labelEn: 'Condenser Coil',
            labelAr: 'كويل الكوندنسر',
            type: 'dropdown',
            options: OK_CLEANED_DIRTY
        },
        {
            key: 'humidifier',
            labelEn: 'Humidifier',
            labelAr: 'المرطب',
            type: 'dropdown',
            options: OK_NOT_OK_NA
        },
        {
            key: 'heaters',
            labelEn: 'Reheat / Heaters',
            labelAr: 'الهياتر اذا وجدت',
            type: 'dropdown',
            options: OK_NA
        },
        {
            key: 'drain',
            labelEn: 'Drain / Condensate Pump',
            labelAr: 'الدرين ومضخة التصريف',
            type: 'dropdown',
            options: OK_BLOCKED
        },
        {
            key: 'leak_detector',
            labelEn: 'Water Leak Detector',
            labelAr: 'حساس تسرب الماء',
            type: 'dropdown',
            options: OK_NOT_OK_NA
        },
        {
            key: 'active_alarms',
            labelEn: 'Active Alarms',
            labelAr: 'الإنذارات الفعالة',
            type: 'text'
        },
        {
            key: 'controls',
            labelEn: 'Controller / Display',
            labelAr: 'لوحة التحكم والشاشة',
            type: 'dropdown',
            options: OK_NOT_OK
        }
    ],
    cooling_tower: [
        {
            key: 'water_in_temp',
            labelEn: 'Water In Temp',
            labelAr: 'حرارة الماء الداخل',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'water_out_temp',
            labelEn: 'Water Out Temp',
            labelAr: 'حرارة الماء الخارج',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'fan_amps',
            labelEn: 'Fan Amps',
            labelAr: 'أمبير المروحة',
            type: 'number',
            unit: 'A'
        },
        {
            key: 'basin_level',
            labelEn: 'Basin Water Level',
            labelAr: 'مستوى الماء',
            type: 'dropdown',
            options: OK_LOW
        },
        {
            key: 'tds',
            labelEn: 'TDS',
            labelAr: 'TDS',
            type: 'number',
            unit: 'ppm'
        },
        {
            key: 'ph',
            labelEn: 'pH',
            labelAr: 'pH',
            type: 'text'
        },
        {
            key: 'conductivity',
            labelEn: 'Conductivity',
            labelAr: 'الناقلية',
            type: 'number',
            unit: 'µS/cm'
        }
    ],
    boiler: [
        {
            key: 'flow_temp',
            labelEn: 'Flow Temp',
            labelAr: 'حرارة الذهاب',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'return_temp',
            labelEn: 'Return Temp',
            labelAr: 'حرارة الرجوع',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'gas_pressure',
            labelEn: 'Gas Pressure',
            labelAr: 'ضغط الغاز',
            type: 'number',
            unit: 'mbar'
        },
        {
            key: 'flue_gas_temp',
            labelEn: 'Flue Gas Temp',
            labelAr: 'حرارة غازات العادم',
            type: 'temperature',
            unit: '°C'
        },
        {
            key: 'burner_amps',
            labelEn: 'Burner Amps',
            labelAr: 'أمبير الموقد',
            type: 'number',
            unit: 'A'
        },
        {
            key: 'co_level',
            labelEn: 'CO Level',
            labelAr: 'CO Level',
            type: 'number',
            unit: 'ppm'
        },
        {
            key: 'co2_level',
            labelEn: 'CO2 Level',
            labelAr: 'CO2 Level',
            type: 'number',
            unit: '%'
        }
    ],
    vrf: [
        {
            key: 'outdoor_amps',
            labelEn: 'Outdoor Unit Amps',
            labelAr: 'أمبير الوحدة الخارجية',
            type: 'number',
            unit: 'A'
        },
        {
            key: 'high_pressure',
            labelEn: 'High Pressure',
            labelAr: 'الضغط العالي',
            type: 'number',
            unit: 'bar'
        },
        {
            key: 'low_pressure',
            labelEn: 'Low Pressure',
            labelAr: 'الضغط المنخفض',
            type: 'number',
            unit: 'bar'
        },
        {
            key: 'refrigerant',
            labelEn: 'Refrigerant Type',
            labelAr: 'نوع الفريون',
            type: 'dropdown',
            options: REFRIGERANT_TYPES
        },
        {
            key: 'indoor_count',
            labelEn: 'Indoor Unit Count',
            labelAr: 'عدد الوحدات الداخلية',
            type: 'number'
        },
        {
            key: 'indoor_status',
            labelEn: 'Indoor Unit Status',
            labelAr: 'حالة الوحدات الداخلية',
            type: 'dropdown',
            options: OK_FAULT
        }
    ]
};
export const PHASES = [
    'Single Phase (1Ph)',
    'Three Phase (3Ph)'
];
export const MAX_COMPRESSORS = 5;
export const MAX_CIRCUITS = 3;
export const MAX_FANS = 5;
const WITH_COMPRESSORS = [
    'chiller',
    'fcu',
    'split',
    'rtu',
    'dehumidifier',
    'ccu'
];
const WITH_AIR_FANS = [
    'ahu',
    'rtu',
    'dehumidifier'
];
const FAN_KINDS = [
    {
        id: 'supply',
        en: 'Supply Fan',
        ar: 'مروحة التزويد',
        countAr: 'عدد مراوح التزويد'
    },
    {
        id: 'extract',
        en: 'Exhaust Fan',
        ar: 'مروحة الطرد',
        countAr: 'عدد مراوح الطرد'
    },
    {
        id: 'return',
        en: 'Return Fan',
        ar: 'مروحة الراجع',
        countAr: 'عدد مراوح الراجع'
    }
];
export function hasUnitConfig(type) {
    return WITH_COMPRESSORS.includes(type) || WITH_AIR_FANS.includes(type);
}
export function defaultUnitConfig(type) {
    return {
        phase: type === 'split' || type === 'fcu' ? '1Ph' : '3Ph',
        compressors: 1,
        circuits: 1,
        fans: {
            supply: 1,
            extract: 0,
            return: 0
        }
    };
}
function countOf(text, min, max, fallback) {
    const n = parseInt(text ?? '', 10);
    return Number.isNaN(n) ? fallback : Math.min(Math.max(n, min), max);
}
export function unitConfigFromValues(type, values) {
    const d = defaultUnitConfig(type);
    const phase = values.unit_phase ?? '';
    return {
        phase: phase.includes('3Ph') ? '3Ph' : phase.includes('1Ph') ? '1Ph' : d.phase,
        compressors: countOf(values.compressor_count, 1, MAX_COMPRESSORS, d.compressors),
        circuits: countOf(values.circuit_count, 1, MAX_CIRCUITS, d.circuits),
        fans: {
            supply: countOf(values.supply_fan_count, 0, MAX_FANS, d.fans.supply),
            extract: countOf(values.extract_fan_count, 0, MAX_FANS, d.fans.extract),
            return: countOf(values.return_fan_count, 0, MAX_FANS, d.fans.return)
        }
    };
}
export function unitConfigValues(config) {
    return {
        unit_phase: PHASES[config.phase === '3Ph' ? 1 : 0],
        compressor_count: String(config.compressors),
        circuit_count: String(config.circuits),
        supply_fan_count: String(config.fans.supply),
        extract_fan_count: String(config.fans.extract),
        return_fan_count: String(config.fans.return)
    };
}
const numbers = (from, to)=>Array.from({
        length: to - from + 1
    }, (_, i)=>String(from + i));
function ampsRows(field, phase) {
    const { perPhase: _perPhase, ...base } = field;
    if (phase === '1Ph') return [
        base
    ];
    return [
        1,
        2,
        3
    ].map((n)=>({
            ...base,
            key: `${base.key}_l${n}`,
            labelEn: `${base.labelEn} L${n}`,
            labelAr: `${base.labelAr} L${n}`
        }));
}
function voltageRows(field, phase) {
    const base = {
        key: field.key,
        labelEn: 'Voltage',
        labelAr: 'الفولتيه',
        type: 'number',
        unit: 'V'
    };
    if (phase === '1Ph') return [
        base
    ];
    return [
        ...[
            'L1-L2',
            'L1-L3',
            'L2-L3'
        ].map((pair)=>({
                ...base,
                key: `${field.key}_${pair.replace('-', '').toLowerCase()}`,
                labelEn: `Voltage ${pair}`,
                labelAr: `الفولتيه ${pair}`
            })),
        {
            ...base,
            key: `${field.key}_ln`,
            labelEn: 'Voltage L-N (Neutral)',
            labelAr: 'الفولتيه فاز - نيوترل'
        }
    ];
}
const PHASE_ROW = {
    key: 'unit_phase',
    labelEn: 'Power Supply (Phase)',
    labelAr: 'نوع الكهرباء (فاز)',
    type: 'dropdown',
    options: PHASES,
    section: 'config'
};
function dynamicRows(type, config) {
    const compressors = WITH_COMPRESSORS.includes(type);
    const airFans = WITH_AIR_FANS.includes(type);
    const rows = [];
    if (compressors) {
        rows.push({
            key: 'compressor_count',
            labelEn: 'Number of Compressors',
            labelAr: 'عدد الضواغط',
            type: 'count',
            options: numbers(1, MAX_COMPRESSORS),
            section: 'config'
        }, {
            key: 'circuit_count',
            labelEn: 'Number of Circuits/Cycles',
            labelAr: 'عدد الدوائر',
            type: 'count',
            options: numbers(1, MAX_CIRCUITS),
            section: 'config'
        });
    }
    if (airFans) {
        FAN_KINDS.forEach((kind)=>rows.push({
                key: `${kind.id}_fan_count`,
                labelEn: `Number of ${kind.en}s`,
                labelAr: kind.countAr,
                type: 'count',
                options: numbers(0, MAX_FANS),
                section: 'config'
            }));
    }
    if (compressors) {
        for(let n = 1; n <= config.compressors; n++){
            rows.push(...ampsRows({
                key: `compressor_${n}_amps`,
                labelEn: `Compressor ${n} Amps`,
                labelAr: `أمبير الضاغط ${n}`,
                type: 'number',
                unit: 'A',
                section: `compressor_${n}`
            }, config.phase));
            if (type === 'chiller') {
                const wanted = config.phase === '3Ph' ? /_voltage_l\dl\d$/ : /_voltage_1ph$/;
                rows.push(...getCompressorFields(n - 1).filter((f)=>wanted.test(f.key)));
                if (config.phase === '3Ph') {
                    rows.push({
                        key: `compressor_${n}_voltage_ln`,
                        labelEn: `Compressor ${n} L-N (Neutral)`,
                        labelAr: `الضاغط ${n} فاز - نيوترل`,
                        type: 'number',
                        unit: 'V',
                        section: `compressor_${n}`
                    });
                }
            }
        }
        for(let n = 1; n <= config.circuits; n++){
            rows.push(...getCircuitFields(n - 1).slice(0, type === 'chiller' ? 4 : 2));
        }
    }
    if (airFans) {
        FAN_KINDS.forEach((kind)=>{
            for(let n = 1; n <= config.fans[kind.id]; n++){
                rows.push(...ampsRows({
                    key: `${kind.id}_fan_${n}_amps`,
                    labelEn: `${kind.en} ${n} Amps`,
                    labelAr: `أمبير ${kind.ar} ${n}`,
                    type: 'number',
                    unit: 'A'
                }, config.phase));
            }
        });
    }
    return rows;
}
export function buildChecklist(type, config = defaultUnitConfig(type)) {
    if (type === 'custom') return [];
    const fixed = STATIC_TEMPLATES[type] ?? [];
    if (!hasUnitConfig(type)) return fixed;
    return [
        PHASE_ROW,
        ...fixed
    ].flatMap((f)=>f === DYNAMIC ? dynamicRows(type, config) : f.perPhase ? ampsRows(f, config.phase) : f.type === 'voltage' ? voltageRows(f, config.phase) : [
            f
        ]);
}
export const CHECKLIST_TEMPLATES = Object.fromEntries(Object.keys(STATIC_TEMPLATES).map((type)=>[
        type,
        buildChecklist(type)
    ]));
export function getChecklistTemplate(type) {
    return buildChecklist(type);
}
function fullChecklist(type, phase) {
    return buildChecklist(type, {
        phase,
        compressors: MAX_COMPRESSORS,
        circuits: MAX_CIRCUITS,
        fans: {
            supply: MAX_FANS,
            extract: MAX_FANS,
            return: MAX_FANS
        }
    });
}
export function unitRowKeys(type) {
    return new Set([
        ...fullChecklist(type, '3Ph'),
        ...fullChecklist(type, '1Ph')
    ].map((f)=>f.key));
}
const LEGACY_FIELDS = [
    {
        key: 'suction_pressure',
        labelEn: 'Suction Pressure',
        labelAr: 'ضغط السحب',
        type: 'pressure'
    },
    {
        key: 'discharge_pressure',
        labelEn: 'Discharge Pressure',
        labelAr: 'ضغط الضخ',
        type: 'pressure'
    },
    {
        key: 'compressor_amps',
        labelEn: 'Compressor Amps',
        labelAr: 'أمبير الضاغط',
        type: 'number',
        unit: 'A'
    },
    {
        key: 'fan_amps',
        labelEn: 'Fan Motor Amps',
        labelAr: 'أمبير محرك المروحة',
        type: 'number',
        unit: 'A'
    },
    {
        key: 'supply_fan_amps',
        labelEn: 'Supply Fan Motor Amps',
        labelAr: 'أمبير محرك مروحة الهواء',
        type: 'number',
        unit: 'A'
    }
];
export const LEGACY_ROW_KEYS = new Set(LEGACY_FIELDS.map((f)=>f.key));
export const UNIT_CONFIG_TYPES = [
    ...new Set([
        ...WITH_COMPRESSORS,
        ...WITH_AIR_FANS
    ])
];
export function getFieldDefinitions(type) {
    if (!hasUnitConfig(type)) return buildChecklist(type);
    return [
        ...fullChecklist(type, '3Ph'),
        ...fullChecklist(type, '1Ph'),
        ...LEGACY_FIELDS,
        ...type === 'chiller' ? buildChillerChecklist(4, 4) : []
    ];
}
export const DEFAULT_ATTENTION_ROWS = 1;
export const FORM_STEPS = [
    {
        step: 1,
        title: 'Report Info',
        titleAr: 'معلومات التقرير'
    },
    {
        step: 2,
        title: 'Machine Data',
        titleAr: 'بيانات المكينة'
    },
    {
        step: 3,
        title: 'Checklist & Readings',
        titleAr: 'قائمة الفحص والقراءات'
    },
    {
        step: 4,
        title: 'Work Performed',
        titleAr: 'الصيانة المطلوبة / العمل المنفذ'
    },
    {
        step: 5,
        title: 'Attention Needed',
        titleAr: 'بحاجة إلى انتباه'
    },
    {
        step: 6,
        title: 'Materials Used',
        titleAr: 'المواد المستخدمة'
    },
    {
        step: 7,
        title: 'Notes',
        titleAr: 'الملاحظات'
    },
    {
        step: 8,
        title: 'Photos',
        titleAr: 'صور الموقع'
    },
    {
        step: 9,
        title: 'Signatures',
        titleAr: 'التوقيعات'
    }
];
export const TOTAL_STEPS = 9;
