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
			<div class="uk-float-right">
			<?=CHtml::ajaxLink('Ver',$this->createUrl('/template/preview',[
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
			<!-- <?
			# TODO
			//=CHtml::link('Editar',$this->createUrl('/template/editar',[
			//	'template'=>$t,
			//]),[
			//	'class'=>'uk-button uk-button-link'
			//]);?> -->
			<?=CHtml::link('Editar',$this->createUrl('/template/editarSaida',[
				'template'=>$t,
			]),[
				'class'=>'uk-button uk-button-link'
			]);?>
			&rarr;
			<?=CHtml::link('Aplicar edição',$this->createUrl('/template/Reprocessar',[
				'template'=>$t,
				'tipo'=>1,
			]),[
				'class'=>'uk-button uk-button-link',
				'onclick'=>'$(this).html("<i class=\"uk-icon-spinner uk-icon-spin\"></i> Reprocessando");',
			]);?>
			<?=CHtml::link('<i class="uk-icon uk-icon-trash"></i>',$this->createUrl('/template/excluir',[
				'template'=>$t,
			]),[
				'class'=>'uk-button uk-button-link',
				'confirm'=>'Certeza?'
			]);?>

			</div>
			<div class="uk-dropdown">
			    <ul class="uk-nav uk-nav-dropdown">
			    	
			    </ul>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<div id="preview" class="uk-modal">
	<div class="uk-modal-dialog uk-modal-dialog-large content">
    </div>
</div>
