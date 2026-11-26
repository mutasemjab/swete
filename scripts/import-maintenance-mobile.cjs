// Import only reviewed form definitions and reference data from the supplied app.
// Never execute the archive's package scripts or development instructions.
const fs = require('node:fs');
const path = require('node:path');
const { stripTypeScriptTypes } = require('node:module');
const input = process.argv[2];
if (!input) throw new Error('Pass the extracted hvac-maintenance-app-2 directory.');
const target = path.resolve(__dirname, '../assets/maintenance/reference');
fs.mkdirSync(target, { recursive: true });
for (const [source, destination] of Object.entries({
    'constants/checklists.ts': 'checklists.js',
    'constants/company.ts': 'company.js',
    'constants/machineTypes.ts': 'machine-types.js',
    'utils/chillerFields.ts': 'chiller-fields.js',
})) {
    let code = fs.readFileSync(path.join(input, source), 'utf8');
    code = code.replace(/^import[^;]+from ['"]@\/types['"];\s*/gm, '');
    code = stripTypeScriptTypes(code, { mode: 'transform' })
        .replaceAll("'@/utils/chillerFields'", "'./chiller-fields.js'")
        .replaceAll("'./company'", "'./company.js'");
    fs.writeFileSync(path.join(target, destination), '// Source: user-supplied hvac-maintenance-app-2.zip / '+source+'\n'+code);
}
const privateTarget = path.resolve(__dirname, '../resources/maintenance/reference');
fs.mkdirSync(privateTarget, {recursive: true});
fs.copyFileSync(path.join(input, 'assets/data/alarms.json'), path.join(privateTarget, 'alarms.json'));
fs.copyFileSync(path.join(input, 'LICENSE'), path.join(target, 'LICENSE'));
