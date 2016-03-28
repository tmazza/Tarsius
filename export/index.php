<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$files = array_filter(scandir('./processados'),function($i){ return (pathinfo($i,PATHINFO_EXTENSION) == 'json'); });
?>
<head>
  <meta charset="iso-8859-1" />
</head>
<body>
  <table>
    <?php foreach ($files as $f):?>
      <tr>
        <td>
          <!-- <img src='http://imagens-concursos.dsi/imagens/concursos/1601-CV/2/cor/<?//=basename(substr($f,0,-5));?>' style="width:120px;" /><br> -->
          <?=basename(substr($f,0,-5))?>
        </td>
        <td><a href="review.php?f=<?=$f?>">Ver</a></td>
      </tr>
    <?php endforeach;  ?>
  </table>
</body>
