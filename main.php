<pre>
<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
// Busca matriz de pixels de forma direta
// http://stackoverflow.com/questions/13791207/better-way-to-get-map-of-all-pixels-of-an-image-with-gd
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once './src/Image.php';
// include_once './src/GeraTemplate.php';

$baseDir = __DIR__ . '/image/processar/';
$files = scandir($baseDir);
unset($files[0]);
unset($files[1]);
$geral =  microtime(true);
$count = 0;
foreach ($files as $f) {
  try {
    $count++;
    # TODO: instanciar somente um objeto de imagem!
    $image = new Image('FAURGS_100_NOVO');
    // $image = new GeraTemplate('FAURGS_100'); # <<<<<<<<<<<<<
    echo '<hr>' .  $f . "\n";
    $image->exec($baseDir . $f);
    $times = ($image->getTimes());
    $total = $times['timeAll'];
    foreach ($times as $n => $t) {
        echo str_pad($n, 30, ' ', STR_PAD_LEFT) . ': ' . number_format($t, 2) . ' - ' . str_pad(number_format((($t / $total) * 100), 1), 5, ' ', STR_PAD_LEFT) . '% - ' . ' - ' . $t . "\n";
    }
    $export = fopen('./export/processados/'.$f.'.json','w');
    fwrite($export,json_encode($image->output));
    fclose($export);

    $regioes = array_map(function($i){ return $i[0]; },$image->output['regioes']);
    echo implode('',$regioes) . "\n";
    echo $count ."\r";
    $image = null;
    unset($image);
  } catch (Exception $ex) {
    echo '<h3>' . $ex->getMessage() . '</h3>';
  }
}
echo "\n----------------------------------------------------------------\n";
echo 'TEMPO TOTAL: ' . number_format((microtime(true) - $geral),2) . "s\n";
echo ' QUANTIDADE: ' . $count . "\n";
echo 'TEMPO MEDIO: ' . number_format(  (microtime(true) - $geral) / $count ,2) . "s\n\n";
?>
