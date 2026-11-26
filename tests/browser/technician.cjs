const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const {spawn, spawnSync} = require('node:child_process');
const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

(async () => {
    const root = path.resolve(__dirname, '../..');
    const temp = fs.mkdtempSync(path.join(os.tmpdir(), 'swete-offline-browser-'));
    const env = {...process.env, APP_ENV:'local', DB_CONNECTION:'sqlite', DB_DATABASE:path.join(temp,'test.sqlite'), DATABASE_URL:'',
        CACHE_DRIVER:'array', MAIL_MAILER:'array', SESSION_DRIVER:'file', SWETE_BROWSER_TEST_ROOT:temp};
    const setup = spawnSync('php', ['tests/browser/setup.php'], {cwd:root, env, encoding:'utf8', windowsHide:true});
    if (setup.status !== 0) throw new Error(setup.stdout+setup.stderr);
    const server = spawn('php', ['-S','127.0.0.1:9087','tests/browser/router.php'], {cwd:root,env,windowsHide:true,stdio:['ignore','pipe','pipe']});
    let serverErrors='';server.stderr.on('data',chunk=>serverErrors+=chunk);server.stdout.on('data',()=>{});
    let browser, page;
    const errors=[], networkFailures=[];
    try {
        for(let i=0;i<50;i++){try{await fetch('http://127.0.0.1:9087/login');break;}catch{await new Promise(resolve=>setTimeout(resolve,100));}}
        browser = await chromium.launch({headless:true, executablePath:process.env.BROWSER_EXECUTABLE || 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe'});
        const context = await browser.newContext({viewport:{width:390,height:844}});
        page = await context.newPage();page.on('pageerror',error=>errors.push(error.message));
        page.on('requestfailed',request=>networkFailures.push(request.url()+': '+request.failure()?.errorText));
        await page.goto('http://127.0.0.1:9087/login');
        await page.locator('[name=email]').fill('browser@example.test');await page.locator('[name=password]').fill('local-browser-test');
        await page.locator('button[type=submit]').click();
        await page.waitForURL('**/m/visits/create');
        await page.locator('[data-action=new]').waitFor();
        await page.evaluate(async()=>{await navigator.serviceWorker.ready;});
        await page.waitForFunction(async()=>{const keys=await caches.keys();return keys.some(key=>key.includes('-shell-'));});
        await page.locator('[data-tab=documents]').click();
        const manualText = page.waitForResponse(response=>response.url().endsWith('/documents/1/text') && response.ok());
        await page.locator('[data-action=download-document]').click();
        await manualText;
        await page.waitForFunction(async()=>{
            const saved=await new Promise(resolve=>{const db=indexedDB.open('asee-technician-1');db.onsuccess=()=>{const request=db.result.transaction('documents').objectStore('documents').get(1);request.onsuccess=()=>{resolve(request.result);db.result.close();};};});
            return !!saved?.pages?.some(page=>page.text.includes('P123'));
        });
        await page.locator('[data-tab=visits]').click();
        await page.locator('[data-action=new]').click();
        await page.locator('[data-visit=customer_id]').selectOption('1');
        await page.locator('[data-action=add-report]').click();
        await page.locator('[data-key=machine_type]').selectOption('ahu');
        await page.locator('[data-key=model]').fill('OFFLINE-AHU');
        await page.locator('[data-action=step][data-id="3"]').first().click();
        await page.locator('[data-field]').first().selectOption({index:2});
        // Go offline, enter work and a photo, reload the whole page, then sign and queue.
        await context.setOffline(true);
        await page.locator('[data-action=step][data-id="4"]').first().click();
        await page.locator('[data-key=solution]').fill('Offline work survives a full reload');
        await page.locator('[data-action=step][data-id="8"]').first().click();
        const photo=await page.evaluate(()=>{const canvas=document.createElement('canvas');canvas.width=240;canvas.height=160;const ctx=canvas.getContext('2d');ctx.fillStyle='#0d766e';ctx.fillRect(0,0,240,160);return canvas.toDataURL().split(',')[1];});
        await page.locator('#photos').setInputFiles({name:'site.png',mimeType:'image/png',buffer:Buffer.from(photo,'base64')});
        await page.locator('.photo-grid img').waitFor();
        await page.locator('[data-action=back]').click();
        await page.reload();await page.locator('[data-action=open]').first().click();
        await page.locator('[data-action=step][data-id="4"]').first().click();
        assert.equal(await page.locator('[data-key=solution]').inputValue(),'Offline work survives a full reload');
        await page.locator('[data-action=step][data-id="8"]').first().click();assert.equal(await page.locator('.photo-grid img').count(),1);
        await page.locator('[data-tab=alarms]').click();
        await page.locator('#alarm-search').fill('P123');
        await page.locator('#alarm-results').getByText('Browser test controller parameter', {exact:false}).waitFor();
        await page.locator('[data-tab=visits]').click();await page.locator('[data-action=open]').first().click();
        await page.locator('[data-action=sign]').click();
        await page.locator('[data-signature-name=customer]').fill('Test customer');
        for(const key of ['technician','customer']){
            const canvas=page.locator(`[data-signature=${key}]`);await canvas.scrollIntoViewIfNeeded();const box=await canvas.boundingBox();
            await page.mouse.move(box.x+20,box.y+30);await page.mouse.down();await page.mouse.move(box.x+150,box.y+90,{steps:12});await page.mouse.up();
        }
        page.once('dialog',dialog=>dialog.accept());await page.locator('[data-action=sign]').click();
        await page.locator('[data-action=print]').waitFor();
        await context.setOffline(false);
        await page.locator('[data-action=sync]').click();
        await page.locator('a[href*="signed-pdf"]').waitFor({timeout:120000});
        await page.screenshot({path:path.join(temp,'technician-signed.png'),fullPage:true});
        await page.locator('[data-action=back]').click();
        await page.screenshot({path:path.join(temp,'technician-home.png'),fullPage:true});
        assert.deepEqual(errors,[]);
        console.log(JSON.stringify({ok:true,checks:['offline reload','local form persistence','photo persistence','offline manual parameter lookup','pointer signatures','queued submission','online sync','signed PDF link'],artifacts:temp}));
    } catch(error){
        console.error(serverErrors.slice(-1500));console.error(errors,networkFailures);
        if(page){console.error((await page.locator('body').innerText()).slice(0,1500));await page.screenshot({path:path.join(temp,'failure.png'),fullPage:true});}
        console.error('Artifacts: '+temp);throw error;
    }
    finally {await browser?.close();server.kill();}
})().catch(error=>{console.error(error);process.exitCode=1;});
