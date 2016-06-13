<?php
// Busca imagens do diretório origem e move para exec cria um diretorio temporario
set_time_limit(0);
ini_set('memory_limit', '2048M');
date_default_timezone_set('America/Sao_Paulo');

$processadores = (int) exec("nproc");
if ($processadores <= 0)
    die("\tNumero de processadores nao pode ser identificado\n");
echo $processadores . ' processador(es) encontrados' . "\n";
$qtdProcessos           = $processadores + ceil(0.25*$processadores);
$tamMaxBlocoPorProcesso = 250;
$espera                 = 1; # em segndos

isset($argv[1]) ? null : die("\tQual a pasta do concurso?\n");
$concurso = $argv[1];

$dirOrigem = '/repositorios/imagens/concursos/' . $concurso . '/cor/';
is_dir($dirOrigem) ? null : die("\tDiretorio '" . $dirOrigem . "' nao encontrado.\n");

$dirExec = __DIR__ . '/exec';
if (!is_dir($dirExec))
    mkdir($dirExec);

$dirReady = $dirExec . '/ready';
if (!is_dir($dirReady))
    mkdir($dirReady);

while (1) {

    $processosNaoFinalizados = getQtdProcessosNaoFinalizados();

    $files         = array_filter(scandir($dirOrigem), function($i)
    {
        return pathinfo($i, PATHINFO_EXTENSION) == 'jpg';
    });
    $jaProcessados = getJaProcessados();
    $files         = array_diff($files, $jaProcessados);

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
                if (rename($dirOrigem . $file, $dirDest . $file)) {
                    //if(copy($dirOrigem.$file,$dirDest.$file)){
                    $jaProcessados[] = $file;
                    setJaProcessados(json_encode($jaProcessados));
                } else {
                    echo 'Arquivo não copiado ' . $file . "\n";
                }
            }
            // Diretorio onde outro programa estará consultando
            rename($dirDest, $dirReady . '/' . $dirHash);
            echo ($i + 1) . ' ';
            //shell_exec('hhvm processa.php ' . $dirHash . ' ' . $dirOrigem);
            shell_exec('hhvm processa.php ' . $dirHash . ' ' . $dirOrigem . ' > /dev/null &');
        }

        echo "\n";
    } else {
        //if($processosParaCriar == 0){
        //  echo " - Limite de {$qtdProcessos} processos atingido. Aguardando...";
        //} else {
        //  echo " - Nenhum arquivo.";
        //}
    }
    echo "\r" . 'Aguardando...';
    sleep($espera);
}

function getQtdProcessosNaoFinalizados()
{
    $qtd = 0;
    $dir = __DIR__ . '/exec/ready';
    if (file_exists($dir)) {
        $files = array_filter(scandir($dir), function($i)
        {
            return $i != '.' && $i != '..';
        });
        $qtd   = count($files);
    }
    return $qtd;
}

function getJaProcessados()
{
    $content = '{}';
    if (file_exists(__DIR__ . '/distirbuidos.json')) {
        $handle  = fopen(__DIR__ . '/distirbuidos.json', 'r');
        $content = fread($handle, filesize(__DIR__ . '/distirbuidos.json'));
        fclose($handle);
    }
    return json_decode($content, true);
}

function setJaProcessados($str)
{
    $handle = fopen(__DIR__ . '/distirbuidos.json', 'w');
    fwrite($handle, $str);
    fclose($handle);
}
