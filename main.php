<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$processar = true;
$nomeTemplate = 'COPERSE_75';
// $nomeTemplate = 'LINHA_BASE';
// $nomeTemplate = 'HCPA_2015_345';
// $nomeTemplate = 'TESTE_FAURGS_100';

if($processar){
  include_once './src/Image.php';
} else {
  echo 'GERAR TEMPLATE' . "\n";
  include_once './src/GeraTemplate.php';
}

$baseDir = __DIR__ . '/image/processar/';
$files = scandir($baseDir);
unset($files[0]);
unset($files[1]);
$geral =  microtime(true);
$count = 0;
foreach ($files as $f) {
  try {
    $count++;
    if($processar){
      $image = new Image($nomeTemplate);
    } else {
      $image = new GeraTemplate($nomeTemplate);
    }
    echo "\n\n\n ----| " .  $f . "\n\n";
    $image->exec($baseDir . $f);
    $times = ($image->getTimes());
    $total = $times['timeAll'];
    foreach ($times as $n => $t) {
      echo str_pad($n, 30, ' ', STR_PAD_LEFT) . ': ' . number_format($t, 2) . ' - ' . str_pad(number_format((($t / $total) * 100), 1), 5, ' ', STR_PAD_LEFT) . '% - ' . ' - ' . $t . "\n";
    }
    $regioes = array_map(function($i){ return $i[0]; },$image->output['regioes']);
    echo implode('',$regioes) . "\n";
    echo str_repeat('_',120) . "\n";
    $image = null;
    unset($image);
  } catch (Exception $ex) {
    echo '**' . $ex->getMessage();
  }
}
echo "\n\n\n";
echo 'TEMPO TOTAL: ' . number_format((microtime(true) - $geral),2) . "s\n";
echo ' QUANTIDADE: ' . $count . "\n";
echo 'TEMPO MEDIO: ' . number_format(  (microtime(true) - $geral) / $count ,2) . "s\n\n";
?>
