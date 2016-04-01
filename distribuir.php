<?php
set_time_limit(0);

do {

  $fonte = __DIR__ . '/image/processar/';
  $pipes = __DIR__ . '/image/do/pipes/';

  $qtdPipes = isset($argv[1]) ? (int) $argv[1] : 4;

  if(!is_dir($pipes)){
    mkdir($pipes);
  }
  $time = microtime(true);

  # busca arquivos
  $files = scandir($fonte);
  $files = array_filter($files,function($f){
    return !is_dir($f) && pathinfo($f,PATHINFO_EXTENSION) == 'jpg';
  });

  # separa para cada diretorio
  $tamanhoBloco = ceil(count($files) / $qtdPipes);
  $blocos = array_chunk($files,$tamanhoBloco);

  # cria diretorios e move imagens
  for($i=0;$i<$qtdPipes;$i++){
      $pipeDir = $pipes . $i . '/';
      if(!is_dir($pipeDir)){
        mkdir($pipeDir);
      }
      $arquivos = isset($blocos[$i]) ? $blocos[$i] : array();
      foreach ($arquivos as $a) {
        copy($fonte.$a,$pipes.$i.'/'.$a);
        #rename($fonte.$a,$pipes.$i.'/'.$a);
      }
      shell_exec('nohup hhvm test_diff.php ' . $i . ' > /dev/null &');
      #shell_exec('nohup php testeInterpretador.php ' . $i . ' > /dev/null &');
  }

  echo "**" . (microtime(true) - $time) . "\n";

  #sleep(600);

} while(false);
