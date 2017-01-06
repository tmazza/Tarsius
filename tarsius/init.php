<?php
$directory = [
    __DIR__ . '/../web/protected/runtime',
    __DIR__ . '/../web/assets',
    __DIR__ . '/../data',
    __DIR__ . '/../data/template',
    __DIR__ . '/../data/runtime',
];
foreach ($directory as $d) {
    echo "Criando diretório '{$d}' ...";
    if(is_readable($d)) {
        echo "ja-existe";
    } else {
        if(is_dir($d)) {
           echo "Diretório {$d} já existe e não é acessível.";
        } else {
            $oldmask = umask(0);
            $ok = mkdir($d, 0777);
            umask($oldmask);
           echo $ok ? 'ok' : 'erro';
        }
    }
    echo "\n";
}