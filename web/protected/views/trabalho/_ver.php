<?php
if(count($erros) > 0){
    $link = CHtml::link('Erros encontrados',$this->createUrl('/trabalho/verErros',[
        'id' => (int) $trabalho->id,
    ]));
    echo CHtml::tag('div',[
    	'class' => 'uk-alert uk-alert-danger',
    ], $link);
}
?>
<?php if($trabalho->status == 2): ?>
	<div class="uk-alert uk-alert-warning">
		<i class="uk-icon uk-icon-spin uk-icon-spinner"></i>&nbsp;
		Parando trabalho. Aguarde. 		
	</div>
<?php else: ?>
	<?php if($naoExportadas > 0): ?>
		Quantidade não exportada: <b><?=$naoExportadas?></b>
		<?=CHtml::link('Exportar',$this->createUrl('/trabalho/ExportaResultado',[
			'id'=>($trabalho->id),
		]));?>
		<br><br>
	<?php endif; ?>

	<?php if($trabalho->distribuindo): ?>
		<div class="uk-alert">
			<i class="uk-icon uk-icon-spin uk-icon-spinner"></i>&nbsp;
			<b>Distribuindo</b> novos formulários
		</div>
	<?php else: ?>
		<?php if($distribuido > 0): ?>
			<?php $razao = number_format(($processado/$distribuido)*100,0); ?>
			<b>
			<?=HView::plural('Processado',$processado)?> <?=$processado?> de 
			<?=$distribuido?> <?=HView::plural('distribuído',$distribuido)?> | <?=$razao?>%
			</b>
			<?php if($razao!=100): ?>
				<progress value='<?=($processado/$distribuido)*100?>' max='100' style='width:100%'></progress>
			<?php endif; ?>
		<?php else: ?>
			<div class="uk-alert">Nenhum formulário distribuído.</div>
		<?php endif; ?>
		<br><br>
	<?php endif; ?>
<?php endif; ?>

<?php if(count($processosAtivos)>0): ?>
	<div class="uk-grid">
		<div class="uk-width-medium-1-3">
			<table class="uk-table uk-table-condensed uk-table-striped">
				<tr><td colspan="2">
				<?=count($processosAtivos)?> processos ativos
				</td></tr>
				<tr><th>Diretório<br>de trabalho</th><th class="uk-text-right">Qtd. atribuída<br> para o processo</th></tr>
				<?php foreach ($processosAtivos as $p):?>
				  <tr>
				    <td><code><?=$p['workDir'];?></code></td>
				    <td class="uk-text-right"><?=$p['qtd'];?></td>
				  </tr>
				<?php endforeach; ?>
			</table>
		</div>
		<div class="uk-width-2-3"></div>
	</div>
<?php endif; ?>