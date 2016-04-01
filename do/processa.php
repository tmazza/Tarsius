<?php
include_once __DIR__ . '/../src/Image.php';
// Busca imagens do diretório origem e move para exec cria um diretorio temporario
set_time_limit(0);
ini_set('memory_limit', '2048M');

if(!isset($argv[1]))
  die("Informe um diretorio de trabalho.\n");

$dirIn = $argv[1];
$dirBase = __DIR__. '/exec/ready/'.$dirIn;

if(!is_dir($dirBase))
  die("Diretorio de trabalho nao encontrado.\n");

$dirDone = __DIR__.'/done';
if(!is_dir($dirDone))
  mkdir($dirDone);

$dirDoneFile = __DIR__.'/done/file';
if(!is_dir($dirDoneFile))
  mkdir($dirDoneFile);

$dirDoneImg = __DIR__.'/done/img';
if(!is_dir($dirDoneImg))
  mkdir($dirDoneImg);

$files = array_filter(scandir($dirBase),function($i) { return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; });

foreach ($files as $i => $f) {
  $start = time();

  $arquivo = $dirBase.'/'.$f;
  $arquivoDest = str_replace('exec/ready/'.$dirIn,'done/img',$arquivo);

  try {
    $image = new Image('HCPA_50');
    $image->exec($arquivo);

    // altera referencia para o arquivo
    $image->output['arquivo'] = $arquivoDest;

    // salva debug do arquivo
    $export = fopen($dirDoneFile.'/'.$f.'.json','w');
    fwrite($export,json_encode($image->output));
    fclose($export);

    $presenca = 'A';
    $str = '';
    foreach ($image->output['regioes'] as $r) { $str .= $r[0]; }

    $tempos = $image->getTimes();
    $tempoExec = $tempos['timeAll'];

  } catch(Exception $e){

    $presenca = '?';
    $str = $e->getMessage();
    $tempoExec = '??';

  }

  // move imagem para pasta de finalizadas
  rename($arquivo,$arquivoDest);

  // Atualiza arquivo compartilhado de resoluções
  $outData = [pathinfo($f,PATHINFO_FILENAME),$presenca,$str,$start,time(),$tempoExec];
  $strOut = implode(';',$outData)."\n";

  $export = fopen(__DIR__.'/out.csv','a');
  fwrite($export,$strOut);
  fclose($export);

}

rmdir ($dirBase);
