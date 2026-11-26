<?php

// Only loaded by rehearse.php, after the TEMP storage/bootstrap checks.
// This must be a disposable local server, never the configured application database.
$port = filter_var(getenv('MAINTENANCE_REHEARSAL_MYSQL_PORT'), FILTER_VALIDATE_INT);
if (! $port || $port < 1024 || $port > 65535 || $port === 3306) {
    throw new RuntimeException('Use a dedicated local rehearsal MySQL port.');
}
$testRoot = realpath(getenv('SWETE_BROWSER_TEST_ROOT'));
$expectedData = $testRoot ? realpath($testRoot.'/mysql-data') : false;
if (! $expectedData || ! str_starts_with(strtolower($expectedData), strtolower(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR))) {
    throw new RuntimeException('MySQL rehearsal requires its own mysql-data directory under TEMP.');
}
$password = getenv('MAINTENANCE_REHEARSAL_MYSQL_PASSWORD');
if ($password === false || $password === '') throw new RuntimeException('Set a password for the temporary MySQL server.');
$pdo = new PDO('mysql:host=127.0.0.1;port='.$port.';charset=utf8mb4', 'root', $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$actualData = realpath(rtrim($pdo->query('SELECT @@datadir')->fetchColumn(), '/\\'));
if (! $actualData || $actualData !== $expectedData) {
    throw new RuntimeException('Connected server is not the temporary rehearsal server. No database changes made.');
}
// No IF NOT EXISTS: refuse to reuse any existing database, and never drop one.
$pdo->exec('CREATE DATABASE swete_maintenance_rehearsal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$connection = 'maintenance_rehearsal_mysql';
config(['database.default' => $connection, 'database.connections.'.$connection => [
    'driver' => 'mysql', 'url' => null, 'host' => '127.0.0.1', 'port' => $port,
    'database' => 'swete_maintenance_rehearsal', 'username' => 'root', 'password' => $password,
    'unix_socket' => '', 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '', 'strict' => true, 'engine' => 'InnoDB', 'options' => [],
]]);
\Illuminate\Support\Facades\DB::purge($connection);
unset($pdo, $password);
