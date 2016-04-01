<?php
// Busca imagens do diretório origem e move para exec cria um diretorio temporario
set_time_limit(0);
ini_set('memory_limit', '2048M');

$dirOrigem = '/repositorios/imagens/concursos/'.'1412-HCPA03'.'/cor/';
$files = array_filter(scandir($dirOrigem),function($i) { return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; });

$tamBloco = ceil(count($files) / 16);

$blocos = array_chunk($files,$tamBloco);

$dirExec = __DIR__.'/exec';
if(!is_dir($dirExec))
  mkdir($dirExec);

$dirReady = $dirExec.'/ready';
if(!is_dir($dirReady))
    mkdir($dirReady);

echo count($files) . ' imagens. ' . count($blocos) . ' blocos de ' . $tamBloco . ' | ';
foreach ($blocos as $i => $bloco) {

  $dirHash = hash('crc32',microtime(true).rand(0,999999));
  $dirDest = $dirExec.'/'.$dirHash.'/';
  // Diretorio temporario
  mkdir($dirDest);
  foreach ($bloco as $file) {
    if(copy($dirOrigem.$file,$dirDest.$file)){
      // TODO: atualizar lista de já processados
    } else {
      echo 'Arquivo não copiado ' . $file . "\n";
    }
  }
  // Diretorio onde outro programa estará consultando
  rename($dirDest,$dirReady.'/'.$dirHash);

  echo ($i+1) . ' ';
  // TODO: disparar programa que processar
  shell_exec('nohup hhvm processa.php ' . $dirHash . ' > /dev/null &');
  echo "\n";

}

echo "\n";
