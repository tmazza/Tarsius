<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');

$db = new PDO('sqlite:tarsius.db');
$setAsProcessada = $db->prepare('UPDATE distribuido SET status = :status WHERE trabalho_id = :trabId AND nome = :nome');
$setStatusProcesso = $db->prepare('UPDATE processo SET status = :status WHERE trabalho_id = :trabId AND pid = :pid');
$getStatusProc = $db->prepare("SELECT status FROM processo WHERE trabalho_id = :trab AND pid = :pid");

include_once __DIR__ . '/../src/Image.php';

if(!isset($argv[1])) die("Informe um diretorio de trabalho.\n");
if(!isset($argv[2])) die("Informe um diretorio de origem.\n");
if(!isset($argv[3])) die("Qual o trabID ?.\n");

$dirIn = $argv[1];
$dirOut = $argv[2];
$trabId = $argv[3];
$dirBase = __DIR__. '/exec/ready/'.$dirIn;

$pid = getmypid();

if(!is_dir($dirBase))
  die("Diretorio de trabalho nao encontrado.\n");

$dirDone = __DIR__.'/done';
if(!is_dir($dirDone))
  mkdir($dirDone);

$dirDoneFile = __DIR__.'/done/file';
if(!is_dir($dirDoneFile))
  mkdir($dirDoneFile);

$files = array_filter(scandir($dirBase),function($i) { return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; });
$count = 0;
$first = true;
foreach ($files as $i => $f) {
  $count++;
  if($first || $count % 50 == 0){
    $getStatusProc->execute([':trab'=>$trabId,':pid'=>$pid]);
    $proc = $getStatusProc->fetch(PDO::FETCH_ASSOC);
    $getStatusProc->closeCursor();
    $first = false;
  }
  if($proc['status'] == 1){
    $start = time();

    $arquivo = $dirBase.'/'.$f;
    // $arquivoDest = str_replace('exec/ready/'.$dirIn,'done/img',$arquivo);
    $arquivoDest = $dirOut.'/'.$f;

    $template = 'LINHA_BASE';

    try {
      $image = new Image($template);
      $image->exec($arquivo);

      // altera referencia para o arquivo
      $image->output['arquivo'] = $arquivoDest;
      $imageOutPut = $image->output;

      $presenca = '1'; # TODO: 1 presente - 2 ausente  (ausente = marcada)
      $str = '';
      foreach ($image->output['regioes'] as $r) { $str .= $r[0]; }
      $str = $str;
      $tempos = $image->getTimes();
      $tempoExec = $tempos['timeAll'];

    } catch(Exception $e){
      $presenca = '?';
      $str = $e->getMessage();
      $tempoExec = '??';
      $imageOutPut = ['erro'=>$e->getMessage()];
    }
    // salva debug do arquivo
    $export = fopen($dirDoneFile.'/'.$f.'.json','w');
    fwrite($export,json_encode($imageOutPut));
    fclose($export);
  }

  // move imagem para pasta de finalizadas

  rename($arquivo,$arquivoDest);
  $result = $setAsProcessada->execute([
    ':status' => $proc['status'] == 1 ? 2 : 3, // Finalizada | Abortado
    ':trabId' => $trabId,
    ':nome'=>basename($f,PATHINFO_FILENAME),
  ]);
  $setAsProcessada->closeCursor();

  fwrite($hhh,"R{$aaa}: {$result} "  . json_encode($setAsProcessada->errorInfo()) . "\n");
}

$setStatusProcesso->execute([
  ':trabId' => $trabId,
  ':pid' => $pid,
  ':status' => 2,
]);
$setStatusProcesso->closeCursor();
rmdir ($dirBase);
