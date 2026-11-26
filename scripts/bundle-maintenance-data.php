<?php

// Build the repository-owned, data-only seed from an already validated private package.
// No SQL execution, database connection, file upload or attachment copy happens here.
if ($argc !== 3) {
    fwrite(STDERR, "Usage: php scripts/bundle-maintenance-data.php PACKAGE_DIR OUTPUT.php\n");
    exit(1);
}
$package = rtrim($argv[1], '/\\');
$manifest = json_decode(file_get_contents($package.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
$mapping = json_decode(file_get_contents($package.'/mapping.reviewed.json'), true, 512, JSON_THROW_ON_ERROR);
$compressed = file_get_contents($package.'/data.json.gz');
$json = gzdecode($compressed);
if ($json === false || ! hash_equals($manifest['sha256'], hash('sha256', $json)) || $mapping['source_sha256'] !== $manifest['sha256']) {
    throw new RuntimeException('Package/mapping checksum mismatch.');
}
$envelope = ['sha256' => $manifest['sha256'], 'mapping' => $mapping, 'data' => base64_encode($compressed)];
$body = "<?php\n\n// Maintenance table data only. No PDF/image binaries, passwords or API tokens.\nreturn ".var_export($envelope, true).";\n";
if (file_exists($argv[2])) throw new RuntimeException('Refusing to overwrite an existing bundled seed.');
if (file_put_contents($argv[2], $body, LOCK_EX) === false) throw new RuntimeException('Could not write bundled seed.');
echo 'Bundled maintenance seed: '.filesize($argv[2])." bytes\n";
