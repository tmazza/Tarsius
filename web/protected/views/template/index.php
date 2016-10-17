<?php
$this->menu = [
	['Novo template',$this->createUrl('/template/criar')],
];
?>
<h2>Templates</h2>
<ul class="uk-list uk-list-striped">
	<?php foreach ($templates as $t): ?>
		<li>
			<?=$t;?>
			<div class="uk-button-group uk-align-right">
			<?=CHtml::ajaxLink('Ver regiões',$this->createUrl('/template/preview',[
				'template'=>$t,
			]),[
				'complete'=>'js:function(html){
					$("#preview .content").html(html);
  					UIkit.modal("#preview").show();					
				}',
				'update'=>'#preview',
			],[
				'class'=>'uk-button uk-button-link'
			]);?>
			<?//=CHtml::link('Editar',$this->createUrl('/template/editar',[
			//	'template'=>$t,
			//]),[
			//	'class'=>'uk-button uk-button-link'
			//]);?>
			<?=CHtml::link('Editar gerador',$this->createUrl('/template/editarSaida',[
				'template'=>$t,
			]),[
				'class'=>'uk-button uk-button-link'
			]);?>
			<div class="uk-button">Reprocessa: </div>
			<?=CHtml::link(' 1',$this->createUrl('/template/Reprocessar',[
				'template'=>$t,
				'tipo'=>1,
			]),[
				'class'=>'uk-button uk-button-link'
			]);?>
			<?=CHtml::link(' 2',$this->createUrl('/template/Reprocessar',[
				'template'=>$t,
				'tipo'=>2,
			]),[
				'class'=>'uk-button uk-button-link'
			]);?>
			<?=CHtml::link(' 4',$this->createUrl('/template/Reprocessar',[
				'template'=>$t,
				'tipo'=>4,
			]),[
				'class'=>'uk-button uk-button-link'
			]);?>
			<div class="uk-button"> | </div>
			<?=CHtml::link('<i class="uk-icon uk-icon-trash"></i>',$this->createUrl('/template/excluir',[
				'template'=>$t,
			]),[
				'class'=>'uk-button uk-button-link',
				'confirm'=>'Certeza?'
			]);?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<div id="preview" class="uk-modal">
	<div class="uk-modal-dialog uk-modal-dialog-large content">
    </div>
</div>
