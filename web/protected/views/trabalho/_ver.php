Status: <?=$trabalho->getLabelStatus();?>
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
        <?php if(isset($faltaProcessar[$p->id])): ?>
          <?php $processadas = $p->qtd-$faltaProcessar[$p->id];?>
          <td>
            <?=number_format($tempoTotal/($processadas>0?$processadas:1),2,',','.');?>seg
          </td>
          <td>
              <div class="uk-grid">
                <div class='uk-width-8-10'>
                  <progress value='<?=(100 - (100 * ($faltaProcessar[$p->id]/$p->qtd)))?>' max='100' style='width:100%'></progress>
                </div>
                <div class='uk-width-1-10'>
                  <?=$processadas .'/'.$p->qtd;?>
                </div>
              </div>
          </td>
        <?php endif; ?>
      <?php else: ?>
        <td>
          <?//=number_format($tempoTotal/($p->qtd>0?$p->qtd:1),2,',','.');?>
        </td>
        <td><small>Finalizado</small></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
</table>