<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<table class="uk-table">
  <?php foreach ($trabalho->processos as $p): ?>
    <?php $cor = $p->status == 1 ? '#7a7' : 'transparent'; ?>
    <tr style="background:<?=$cor?>"><td><?=implode('</td><td>',$p->attributes)?></td>
      <?php if(isset($faltaProcessar[$p->id])) echo '<td>' . $faltaProcessar[$p->id] . '</td>'; ?>
     </tr>
  <?php endforeach; ?>
</table>
