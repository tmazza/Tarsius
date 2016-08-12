<?php 
$qtdNaoExportados = $trabalho->getNaoExportados(); 
if($qtdNaoExportados > 0){
  echo "<b>{$qtdNaoExportados} arquivo"
  . ($qtdNaoExportados>1?'s':'')
  . " na fila de exportação</b>";
}
?>
<br>
Status: <?=$trabalho->getLabelStatus();?> | Última atualizaçao <?=date('H:i:s',time());?>

<?php if(count($trabalho->processos) > 0): ?>
  <table class="uk-table uk-table-condensed uk-table-striped">
    <?php foreach ($trabalho->processos as $p): ?>
    	<tr>	
        <td><?=$p->pid;?></td>    
      	<td><code><?=$p->workDir;?></code></td>
        <td><?php
          if($p->status == 1){
            $tempoTotal = time() - $p->dataInicio;          
          } else {
            $tempoTotal = $p->dataFim - $p->dataInicio;
          }
          echo str_pad(floor($tempoTotal/60), 2,'0',STR_PAD_LEFT);
          echo ':';
          echo str_pad(($tempoTotal%60), 2,'0',STR_PAD_LEFT);
        ?></td>   
        <?php if($p->status == 1): ?>
          <?php if(isset($faltaProcessar[$p->workDir])): ?>
            <?php $processadas = $p->qtd-$faltaProcessar[$p->workDir];?>
            <td>
              <?=number_format($tempoTotal/($processadas>0?$processadas:1),2,',','.');?>seg
            </td>
            <td>
                <div class="uk-grid">
                  <div class='uk-width-8-10'>
                    <progress value='<?=(100 - (100 * ($faltaProcessar[$p->workDir]/$p->qtd)))?>' max='100' style='width:100%'></progress>
                  </div>
                  <div class='uk-width-1-10'>
                    <?=$processadas .'/'.$p->qtd;?>
                  </div>
                </div>
            </td>
          <?php endif; ?>
        <?php else: ?>
          <td>
            <?=number_format($tempoTotal/($p->qtd>0?$p->qtd:1),2,',','.');?>
          </td>
          <td><small>Finalizado</small></td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else: ?>
  <br><br>
  <b>Nenhum processo ativo.</b>
<?php endif; ?>