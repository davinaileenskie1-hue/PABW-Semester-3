<?php
$path = __DIR__.'/../postman/SiPustaka_API_707012500135.postman_collection.json';
$j = json_decode(file_get_contents($path), true);
if (! is_array($j)) {
    fwrite(STDERR, "JSON tidak valid\n");
    exit(1);
}
$n = 0;
$e = 0;
$missing = [];
foreach ($j['item'] as $f) {
    foreach ($f['item'] as $it) {
        $n++;
        if (isset($it['response'])) {
            $e++;
        }
        $body = $it['request']['body'] ?? [];
        foreach ($body['formdata'] ?? [] as $field) {
            if (($field['type'] ?? '') === 'file' && ! file_exists($field['src'])) {
                $missing[] = $field['src'];
            }
        }
    }
}
echo 'request: '.$n.PHP_EOL;
echo 'contoh response: '.$e.PHP_EOL;
echo 'variabel: '.implode(', ', array_column($j['variable'], 'key')).PHP_EOL;
echo 'berkas lampiran hilang: '.(empty($missing) ? 'tidak ada' : implode(', ', $missing)).PHP_EOL;
