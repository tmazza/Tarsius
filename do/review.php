<?php
include_once __DIR__ . '/../src/Helper.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if(isset($_REQUEST['f'])){

  $reviewImage = __DIR__.'/done/img/'.substr($_REQUEST['f'],0,-9) . '.png';

  $handle = fopen(__DIR__.'/done/file/'.$_REQUEST['f'],'r');
  $json = fread($handle,filesize (__DIR__.'/done/file/'.$_REQUEST['f']));
  fclose($handle);
  $output = json_decode($json,true);

  $original = imagecreatefromjpeg(str_replace('pipes','1402',$output['arquivo']));
  if(!file_exists($reviewImage)){

    $escala = $output['escala'];
    $template = include __DIR__ . '/../template/' . $output['template'] . '.php';

    $regioes = $output['regioes'];
    foreach ($regioes as $r) {
      if($r[0] == 0) { # tipo elipse
        $w = $escala * $template['elpLargura'] ;
        $h = $escala * $template['elpAltura'];
        list($x,$y) = Helper::rotaciona([$r[2],$r[3]],$output['ancoras'][1],$output['rotacao']);
        if($r[1] > 0.25) { # todo: adicionar taxa de PREENCHIMENTO_MINIMO no template!
          imagefilledellipse($original,$x,$y,$w,$h, imagecolorallocate($original, 255,255,0));
        } else {
          imageellipse($original,$x,$y,$w,$h, imagecolorallocate($original, 255,0,255));
        }
      } else {
        echo '?'; #tipo desconhecido
      }
    }
    imagepng($original,$reviewImage);
  }
} else {
  echo 'F! :( ';
  exit;
}
?>

<head>
  <meta charset="iso-8859-1" />
</head>
<body>
  <?php
   $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($reviewImage));
  ?>
  <input id="size" type="range" value='800' min="100" max="<?=imagesx($original)?>" step="10" onchange="document.getElementById('main').style.width = this.value + 'px';"/>
  <br>
  <img id='main' src="<?=$base64;?>" style="width:400px;" />
</body>
