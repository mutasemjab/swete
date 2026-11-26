<?php

// This bootstrap cannot run against the configured application database.
$testRoot = getenv('SWETE_BROWSER_TEST_ROOT');
if (! $testRoot || ! str_starts_with(strtolower(realpath($testRoot) ?: ''), strtolower(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR))) {
    throw new RuntimeException('Browser tests require their own directory under the system temporary directory.');
}
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
foreach (['framework/views', 'framework/sessions', 'framework/cache', 'logs', 'app'] as $directory) {
    if (! is_dir($testRoot.'/storage/'.$directory)) mkdir($testRoot.'/storage/'.$directory, 0775, true);
}
$app->useStoragePath($testRoot.'/storage');
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config([
    'database.default' => 'sqlite', 'database.connections.sqlite.url' => null,
    'database.connections.sqlite.database' => $testRoot.'/test.sqlite',
    'filesystems.default' => 'local', 'filesystems.disks.local.root' => $testRoot.'/storage/app',
    'filesystems.disks.maintenance_private.root' => $testRoot.'/storage/private',
    'session.driver' => 'file', 'session.files' => $testRoot.'/storage/framework/sessions',
    'session.secure' => false, 'session.domain' => null, 'session.cookie' => 'swete_browser_test',
    'cache.default' => 'array', 'activitylog.enabled' => false, 'mail.default' => 'array',
    'app.url' => 'http://127.0.0.1:9087',
]);
\Illuminate\Support\Facades\DB::purge('sqlite');
app('url')->forceRootUrl('http://127.0.0.1:9087');
return $app;
