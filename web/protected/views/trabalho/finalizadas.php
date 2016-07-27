<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/ver',['id'=>$trabalho->id])],
];
?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<table class="uk-table uk-table-condensed uk-table-striped">
	<?php foreach ($trabalho->distribuidos as $d): ?>
		<tr>
			<td><?=$d->nome;?></td>
			<td><?=$d->status;?></td>
			<td><?=str_pad($d->dataFechamento-$d->dataDistribuicao, 2,"0",STR_PAD_LEFT) . 's | ';?></td>
			<td>
				<?php
				if($d->status == 2)
					echo CHtml::link('Ver',$this->createUrl('/distribuido/ver',['id'=>$d->id])) . '<br>';
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>