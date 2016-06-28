<pre>
<?php
set_time_limit(0);
ini_set('memory_limit', '4000M');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$dir = $_GET['a'];
// $dir = "1513-banrisul2";

$compareFile = __DIR__.'/'.$dir.'/index.php';

if(!file_exists($compareFile) || (isset($_GET['op']) && $_GET['op'] == 'rep')){
  $base = __DIR__.'/'.$dir.'/done/file';
  if(is_dir($base)){
    $files = array_filter(scandir($base),function($i){ return (pathinfo($i,PATHINFO_EXTENSION) == 'json'); });
  } else {
    $files = [];
  }

  $CORTE_PRETO = $MATCH_ANCORA = $PREENCHIMENTO_MINIMO = $RESOLUCAO_IMAGEM = false;

  $resultados = [];
  $count = 0;
  foreach ($files as $f) {
    // echo $count . "\r";
    if($count < 10000000){
      $baseName = trim(str_replace('.jpg','',pathinfo($f,PATHINFO_FILENAME)));

      $data = getFileData($base.'/'.$f);

      if(isset($data['erro'])){
        $resultados[$baseName] = $data;
      } else {
        $str = ''; foreach ($data['regioes'] as $r) { $str .= $r[0]; } # concatena string de respostas
        $resultados[$baseName] = $str;

        if(!$CORTE_PRETO){
          $CORTE_PRETO          = $data['CORTE_PRETO'];
          $MATCH_ANCORA         = $data['MATCH_ANCORA'];
          $PREENCHIMENTO_MINIMO = $data['PREENCHIMENTO_MINIMO'];
          $RESOLUCAO_IMAGEM     = $data['RESOLUCAO_IMAGEM'];
        }
      }
      $count++;
    } else {
      break;
    }
  }

  // $h=fopen('temp.json','w');
  // fwrite($h,json_encode($resultados));
  // fclose($h);
  // exit;
  //
  // $h=fopen('temp.json','r');
  // $resultados = fread($h,filesize('temp.json'));
  // fclose($h);
  // $resultados = json_decode($resultados,true);

  // Resultados finais do concurso
  list($concurso,$titulo) = explode('-',$dir);

  $dadosConcurso = getConcData($concurso);
  echo "Conc data loaded\n";

  $status = [
    'CORTE_PRETO'          => $CORTE_PRETO,
    'MATCH_ANCORA'         => $MATCH_ANCORA,
    'PREENCHIMENTO_MINIMO' => $PREENCHIMENTO_MINIMO,
    'RESOLUCAO_IMAGEM'     => $RESOLUCAO_IMAGEM,
    'total'                => count($dadosConcurso),
    'iguais'               => 0,
    'diffAntesBanca'       => 0,
    'diffPosBanca'         => 0,
    'naoIdentificadas'     => 0,
    'notFound'             => 0,
  ];

  $html = '<table border=1>';
  $html .= '<tr><th>NomeArq</th><th>Questoes diferentes <br>Antes da banca</th><th>Detalhe do processamento</th><th>Depois da banca</th></tr>';
  $naoEncontradas = $naoIdentificadas = $diffsLeitura = [];
  $count = 0;
  foreach ($dadosConcurso as $d) {
    $count++;
    // echo $count . "\r";
    $nomeArq = $d['NomeArquivo'];
    if(isset($resultados[$nomeArq])){
      if(is_array($resultados[$nomeArq])){ # array contendo a mensagem de erro
        $status['naoIdentificadas']++;
        $naoIdentificadas[] = [$nomeArq,$resultados[$nomeArq]['erro']];
      } else {
        $igual = $resultados[$nomeArq] == $d['RespostasOriginais'];

        if($igual){
          $status['iguais']++;
        } else {

          $diffsLeitura[] = $nomeArq;

          $status['diffAntesBanca']++;

          // Diferencas antes da banca
          $respAvaliada = str_split($resultados[$nomeArq]);
          $respEsperada = str_split($d['RespostasOriginais']);
          $diffTablePreBanca = getDiffs($respAvaliada,$respEsperada);

          // Diferencas apos a banca | TODO: diferencas depois da banca deve ser idependente das diferenças antes da banca!!! tirar do else!!!

          // Verifica se folha tem limite de questões # aplicado somente após o 'agrupamento' das alternativas
          $respAvaAgrupadas = agrupaResposta($resultados[$nomeArq]);
          if(!is_null($d['qstFol']) && $d['qstFol'] > 0){ # corta pelo num qst na folha
            $respAvaAgrupadas = substr($respAvaAgrupadas,0,$d['qstFol']);
          } elseif(!is_null($d['qstCargo']) && $d['qstCargo'] > 0) { // #corta pelo num qst no cargo
            $respAvaAgrupadas = substr($respAvaAgrupadas,0,$d['qstCargo']);
          }
          $respPosBanca = str_split($d['RespEfetiva']);
          $respAvaAgrupadas = str_split($respAvaAgrupadas);
          $diffTablePosBanca = getDiffs($respAvaAgrupadas,$respPosBanca,false);
          if($diffTablePosBanca != '=') $status['diffPosBanca']++;
          // FIM - Diferencas apos a banca

          // Link para imagem
          $linkImg = "<a href='../review.php?a={$dir}&f={$nomeArq}.jpg.json&r={$PREENCHIMENTO_MINIMO}'>Ver processamento da imagem</a>";
          // Linha da tabela de diferencas
          $html .= "<tr><td>{$nomeArq}</td><td>{$diffTablePreBanca}</td><td>{$linkImg}</td><td>{$diffTablePosBanca}</td></tr>";

        }
      }
    } else { $status['notFound']++; $naoEncontradas[] = $nomeArq; }
  }
  $html .= '</table>';
  echo "Dados conc ok\n";

  echo "Montando HTML\n";
  $allHtml = '<pre><h4>Concurso '.$concurso.'</h4><p style="text-align:right"><a href="../index.php?a='.$dir.'&op=rep">Recriar arquivo</a></p>';
  $allHtml .= 'Resumo:<br><table border=1>' . implode('',array_map(function($a,$b,$c){
    return "<tr><td>{$a}</td><td>{$b}</td><td style='text-align:right'>{$c}</td></tr>";
  },array_keys($status),$status,array_map(function($i,$k) use($status){
    $especials = ['CORTE_PRETO','MATCH_ANCORA','PREENCHIMENTO_MINIMO','RESOLUCAO_IMAGEM'];
    if(in_array($k,$especials)){
      return $i;
    } else {
      return number_format(($i / $status['total'])*100,2,',','.') . '%';
    }
  },$status,array_keys($status)))) . '</table>';

  echo "Finished crazy array_map\n";



  $allHtml .= '<ul>
    <li>notFound: registro em RECEPCAO_FOLHA_CANDIDATO que nao foram processados/encontrados ao realizar o teste. estao listadas no rodape da pagina.</li>
    <li>diffAntesBanca: comparacao entre RespostasOriginais de LEITURA e a interpretacao do Tarsius  para cada uma das regioes do template (e.g 20 questoes = 20*5 regioes)</li>
    <li>diffPosBanca: comparacao entre o resultado final (RespEfetiva de RECEPCAO_FOLHA_CANDIDATO) e a interpretacao do Tarsius (com string agrupada por questao).
    <br> Resultado "=" significa que a interpretacao do Tarsius esta igual ao que foi avaliado apos a banca
    </li>
    <li>naoIdentificadas: erro ao identificar ancoras</li>
  </ul>';
  $allHtml .=  '<hr><br>Diferentes:<br>';
  $allHtml .= $html;


  echo "Nao encontradas ...";
  $allHtml .= '<hr>Nao encontradas<br>' . implode('<br>',array_map(function($arr){
      return implode(',',$arr);
  },array_chunk($naoEncontradas,5)));
  echo "feito \\o/ \n";

  $imgDir = __DIR__.'/'.$dir.'/done/img/';
  // $imgDir = "/repositorios/{$dir}/imagens/concursos/1605-TJM/cor/";

  echo "Nao identificadas ...";
  $allHtml .= '<hr>Nao identificadas<br>';
  foreach ($naoIdentificadas as $nid) {
    $allHtml .= '<br>';
    $imgContent = base64_encode(file_get_contents($imgDir.$nid[0].'.jpg'));
    // $imgContent = '';
    $allHtml .= '<a target="_blank" href="data:image/png;base64,'.$imgContent.'">'.$nid[0].'</a> - ' . $nid[1];

  }

  $allHtml .=  array_map(function($i) use($dir,$imgDir){
      $imgContent = base64_encode(file_get_contents($imgDir.$i[0].'.jpg'));
       return '<a target="_blank" href="data:image/png;base64,'.$imgContent.'">'.$i[0].'</a> - ' . $i[1];
     },$naoIdentificadas);
  echo "feito \\o/ \n";

  echo "Removendo imagens ...";
  # echo "Nao aplicado.";
  $imagensMantidas = array_flip(array_merge(array_column($naoIdentificadas,0),$diffsLeitura));
  foreach ($files as $f) {
    $f = str_replace('.jpg.json','',$f);
    if(!isset($imagensMantidas[$f])){
      unlink(__DIR__.'/'.$dir.'/done/img/'.$f.'.jpg');
    }
  }
  echo "feito \\o/ \n";

  $handle = fopen($compareFile,'w');
  fwrite($handle,$allHtml);
  fclose($handle);

}

/**
 * Agrupa respostas decidindo se W, M ou válida
 */
function agrupaResposta($respAvaliada){
  $questoes = array_chunk(str_split($respAvaliada),5);
  $agrupa = function($alts){
      $countValues = array_count_values($alts);
      if(count($countValues)==1) # tem apenas 1. Ou é tudo W ou é tudo uma das alternativas válidas
        return key($countValues) == 'W' ? 'W' : 'M';
      if(count($countValues)>2) # tem mais de 2
        return 'M';
      if(!isset($countValues['W'])) # tem dois, mas nenhum é W
        return 'M';
      unset($countValues['W']); # garatidamente tenho 2 e um dele é W. Remove-se W
      return key($countValues); # retorna altertiva sobrevivente \o/
  };
  return implode('',array_map($agrupa,$questoes));
}

/**
 * Compara resultados do tarsius com teleform regiao a regiao.
 */
function getDiffs($respAvaliada,$respEsperada,$agrupa=true){
  $respDiffs = array_map(function($a,$b){ return $a == $b;  },$respAvaliada,$respEsperada);
  // Diff interpretacao para cada elipse questões
  $microTable = '<table style="text-align:center;">';
  $microTable .= '<tr><th>Nr</th><th>'.($agrupa?'Teleform':'Final').'</th><th>Tarsius</th></tr>';
  $diffs = 0;
  foreach ($respDiffs as $k => $v) {
    if(!$v){
      $diffs++;
      $numQst = $agrupa ? ceil(($k+1)/5) : $k+1;
      $nomeQst = $numQst;
      if($agrupa){
        $char = 'A'; for($i=0;$i<($k%5);$i++) { $char++; }
        $nomeQst .= '.'.$char;
      }
      $cor = $respEsperada[$k] == $respAvaliada[$k] ? 'green' : 'transparent';

      $microTable .= "<tr style='background:{$cor}'><td>{$nomeQst}</td><td>".$respEsperada[$k]."</td><td>".$respAvaliada[$k]."</td></tr>";
    }
  }
  $microTable .= '</table>';
  return $diffs > 0 ? $microTable : '=';
}



/**
 * Abre,lê e fecha arquivo resultado de um imagem
 */
function getFileData($f){
  $handle = fopen($f,'r');
  $data = fread($handle,filesize($f));
  fclose($handle);
  return json_decode($data,true);
}

/**
 * NomeArquivo
 * qstFol - NrQuestoes em folha
 * qstCargo - NrQuestoes em cargo
 * RespostasOriginais - Interpretacao de todas as elipses
 * RespOriginal - Respostas agrupadas 5 a 5
 * RespEfetiva  - Resposta final após banca
 */
function getConcData($concurso){
  $sql = <<<SQL
SELECT l.NomeArquivo,f.NrQuestoes as qstFol,c.NrQuestoes as qstCargo,l.RespostasOriginais,rfc.RespOriginal,rfc.RespEfetiva
FROM RECEPCAO_FOLHA_CANDIDATO rfc
JOIN LEITURA l ON l.IdLeitura = rfc.IdLeitura
JOIN CARGO c ON c.CodCargo = l.Cargo and c.Concurso = l.Concurso
JOIN FOLHA f ON f.NrFolha = l.FolhaLeitura AND f.NrParte = l.ParteLeitura AND f.Concurso = l.Concurso WHERE rfc.Concurso = {$concurso} AND rfc.IdLeitura IS NOT NULL ORDER BY NomeArquivo ASC
SQL;
  return consulta($sql);
}

/**
 * Executa SQL
 */
function consulta($query){
  $ip = "143.54.100.8";
  $conexao = mssql_connect($ip,"tiago.mazzarollo",base64_decode("VTF4MnVXWDM="));
  // $conexao = mssql_connect($ip,"teleform",'[teleform*1007]');
  mssql_select_db("CONCURSOS",$conexao);
  $result = mssql_query($query,$conexao);
  if(!$result) die('Erro de SQL: ' . $query);
  $data = [];
  for ($i = 0; $i < mssql_num_rows( $result ); ++$i) {
    $data[] = mssql_fetch_assoc($result);
  }
  return $data;
}


header('Location: ' . $dir .'/index.php');
