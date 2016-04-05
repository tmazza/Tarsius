<?php
// Busca imagens do diretório origem e move para exec cria um diretorio temporario
set_time_limit(0);
ini_set('memory_limit', '2048M');
date_default_timezone_set('America/Sao_Paulo');

$qtdProcessos = 4;
$espera = 30; # em segndos

isset($argv[1]) ? null : die("\tQual a pasta do concurso?\n");
$concurso = $argv[1];

$dirOrigem = '/repositorios/imagens/concursos/'.$concurso.'/cor/';
is_dir($dirOrigem) ? null : die("\tDiretorio '" . $dirOrigem . "' nao encontrado.\n");

while(1){

  echo "\n" . date("H:i:s");

  $files = array_filter(scandir($dirOrigem),function($i) { return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; });
  $jaProcessados = getJaProcessados();
  $files = array_diff($files,$jaProcessados);

  if(count($files) > 0){
    echo "\n----------------------------------------------------------------\n";
    // $files = array_slice($files,0,10);

    $tamBloco = ceil(count($files) / $qtdProcessos);
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
          $jaProcessados[] = $file;
          setJaProcessados(json_encode($jaProcessados));
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
  } else {
    echo " - Nenhum arquivo.";
  }
  echo "\n";
  sleep($espera);
}

function getJaProcessados(){
  $content = '{}';
  if(file_exists(__DIR__.'/distirbuidos.json')){
    $handle = fopen(__DIR__.'/distirbuidos.json','r');
    $content = fread($handle,filesize(__DIR__.'/distirbuidos.json'));
    fclose($handle);
  }
  return json_decode($content,true);
}

function setJaProcessados($str){
  $handle = fopen(__DIR__.'/distirbuidos.json','w');
  fwrite($handle,$str);
  fclose($handle);
}
