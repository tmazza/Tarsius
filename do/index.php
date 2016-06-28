
<ul>
  <?php
  $base = __DIR__.'/finalizados';
  $files = scandir($base);
  foreach ($files as $f) {
    if (strlen($f) > 2 && is_dir($base.'/'.$f.'/'))
      echo '<li><a href="finalizados/index.php?a='.$f.'">'.$f.'</a></li>';
  }
  ?>
</ul>
