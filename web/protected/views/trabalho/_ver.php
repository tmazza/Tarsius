
<?php
$distribuido;
$processado = $processado > 0 ? $processado : 1;
?>
Processado(s) <?=$processado?> de <?=$distribuido?> distribuído(s) | <?=number_format(($processado/$distribuido)*100,0,',','.')?>%
<progress value='<?=($processado/$distribuido)*100?>' max='100' style='width:100%'></progress>

<?php if(count($processosAtivos)>0): ?>
  <table class="uk-table uk-table-condensed uk-table-striped">
    <tr><td colspan="2">Processos ativos</td></tr>
    <tr><th>Diretório</th><th class="uk-text-right">Qtd. atribuída</th></tr>
    <?php foreach ($processosAtivos as $p):?>
      <tr>
        <td><code><?=$p['workDir'];?></code></td>
        <td class="uk-text-right"><?=$p['qtd'];?></td>
      </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>