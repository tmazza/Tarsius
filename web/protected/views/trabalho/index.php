<?php
$this->menu = [
	['Novo trabalho',$this->createUrl('/trabalho/novo')],
	['Configurações de processamento',$this->createUrl('/perfil/index')],
];
?>
<h2>Trabalhos</h2>
<?php foreach($trabalhos as $t): ?>
	<div class="uk-panel uk-panel-box">
		<h3><?=$t->nome?>
			<div class="uk-button-group uk-float-right">
				<?=CHtml::link('Configurar',$this->createUrl('/trabalho/editar',['id'=>$t->id,]),[
					'class'=>'uk-button uk-button-small'
				]);?>
				<?=CHtml::link('Ver',$this->createUrl('/trabalho/ver',[
					'id'=>$t->id,
				]),[
					'class'=>'uk-button uk-button-small uk-button-primary'
				]);?>		
			</div>
		</h3>
		<?php
		$this->widget('zii.widgets.CDetailView', array(
		    'data'=>$t,
		    'attributes'=>Trabalho::detailView($t),
		));
		?>
	</div>
<?php endforeach; ?>