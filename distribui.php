#!/usr/bin/php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '2048M');
date_default_timezone_set('America/Sao_Paulo');

if(!isset($argv[1])) die("Qual o trabalho?\n");
$trabId = $argv[1];

$db = new PDO('sqlite:'.__DIR__.'/tarsius.db');
$getTrabalho = $db->prepare('SELECT * FROM trabalho WHERE id = :trabId');
$addDistribuido = $db->prepare('INSERT INTO distribuido (trabalho_id,nome,status,tempDir) VALUES (:trabId,:nome,:status,:tempDir)');
$getJaDistribuido = $db->prepare('SELECT nome FROM distribuido WHERE trabalho_id = :trabId');
$criarProcesso = $db->prepare('INSERT INTO processo (pid,status,trabalho_id,workDir,qtd) VALUES (:pid,:status,:trabId,:workDir,:qtd)');

// Busca imagens do diret�rio origem e move para exec cria um diretorio temporario

$processadores = (int) exec("nproc");
if ($processadores <= 0)
    die("\tNumero de processadores nao pode ser identificado\n");
echo $processadores . ' processador(es) encontrados' . "\n";
$qtdProcessos           = $processadores + ceil(0.25*$processadores);
$tamMaxBlocoPorProcesso = 250;

$getTrabalho->execute([':trabId'=>$trabId]);
$data = $getTrabalho->fetch(PDO::FETCH_ASSOC);
$getTrabalho->closeCursor();
$espera = $data['tempoDistribuicao'];

if(is_null($data['sourceDir']))
  die("\tQual a pasta do concurso?\n");

$dirOrigem = $data['sourceDir'];
is_dir($dirOrigem) ? null : die("\tDiretorio '" . $dirOrigem . "' nao encontrado.\n");

$dirExec = __DIR__ . '/exec';
if (!is_dir($dirExec))
    mkdir($dirExec);

$dirReady = $dirExec . '/ready';
if (!is_dir($dirReady))
    mkdir($dirReady);
$keepRunning = true;
while ($keepRunning) {

    $processosNaoFinalizados = getQtdProcessosNaoFinalizados();

    $files = array_filter(scandir($dirOrigem), function($i){
        return pathinfo($i, PATHINFO_EXTENSION) == 'jpg';
    });
    $jaProcessados = getJaProcessados($trabId,$getJaDistribuido);
    $files = array_diff($files, $jaProcessados);

    $processosParaCriar = $qtdProcessos - $processosNaoFinalizados;

    if ($processosParaCriar > 0 && count($files) > 0) {

        $tamBlocoPorProcesso = ceil(count($files) / $processosParaCriar);
        if ($tamBlocoPorProcesso > $tamMaxBlocoPorProcesso) {
            $tamBlocoPorProcesso = $tamMaxBlocoPorProcesso;
        }

        $files    = array_slice($files, 0, $tamBlocoPorProcesso * $processosParaCriar); // Limite quantidade processa
        $tamBloco = ceil(count($files) / $processosParaCriar); # quebra em n pedacos de acordo com o tamnho da entrada
        $blocos   = array_chunk($files, $tamBloco);

        echo "\r * " . str_pad(count($files), 5,' ', STR_PAD_LEFT) . ' imagens. ' . str_pad(count($blocos), 3,' ', STR_PAD_LEFT) . ' blocos de ' . str_pad($tamBloco, 3,' ', STR_PAD_LEFT) . ' | ';
        foreach ($blocos as $i => $bloco) {

            $dirHash = hash('crc32', microtime(true) . rand(0, 999999));
            $dirDest = $dirExec . '/' . $dirHash . '/';
            // Diretorio temporario
            mkdir($dirDest);
            foreach ($bloco as $file) {
                if (rename($dirOrigem . '/' . $file, $dirDest . $file)) {
                    setJaProcessados($trabId,$file,$addDistribuido,$dirHash);
                } else {
                    echo 'Arquivo n�o copiado ' . $file . "\n";
                }
            }
            // Diretorio onde outro programa estar� consultando
            rename($dirDest, $dirReady . '/' . $dirHash);
            echo ($i + 1) . ' ';

            //shell_exec('hhvm processa.php ' . $dirHash . ' ' . $dirOrigem);
            $cmd = 'php processa.php ' . $dirHash . ' ' . $dirOrigem . ' ' . $trabId;
            $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');
            $criarProcesso->execute([
              ':pid'=>$pid,
              ':trabId'=>$trabId,
              ':status'=>1,
              ':workDir'=>$dirHash,
              ':qtd'=>count($bloco),
            ]);
            $criarProcesso->closeCursor();
        }

        echo "\n";
    }

    $getTrabalho->execute([':trabId'=>$trabId]);
    $data = $getTrabalho->fetch(PDO::FETCH_ASSOC);
    $getTrabalho->closeCursor();
    if($data['status'] != 1){
      $keepRunning = false;
      $espera = $data['tempoDistribuicao'];
    }
    echo "\r" . 'Aguardando...';
    sleep($espera);
}

$setStatusTrabalho = $db->prepare('UPDATE trabalho SET status = :status WHERE id = :trabId');
$setStatusTrabalho->execute([
   ':trabId'=>$trabId,
   ':status'=>0,
]);
$setStatusTrabalho->closeCursor();

function getQtdProcessosNaoFinalizados()
{
  $qtd = 0;
  $dir = __DIR__ . '/exec/ready';
  if (file_exists($dir)) {
      $files = array_filter(scandir($dir), function($i){
          return $i != '.' && $i != '..';
      });
      $qtd   = count($files);
  }
  return $qtd;
}

function getJaProcessados($trabId,$getJaDistribuido)
{
  $getJaDistribuido->execute([':trabId'=>$trabId]);
  $data = array_column($getJaDistribuido->fetchAll(),'nome');
  $getJaDistribuido->closeCursor();
  return $data;
}

function setJaProcessados($trabId,$str,$addDistribuido,$dirHash)
{
  $addDistribuido->execute([
    ':trabId'=>$trabId,
    ':nome'=>$str,
    ':status'=>1,
    ':tempDir'=>$dirHash,
  ]);
  $addDistribuido->closeCursor();
}
