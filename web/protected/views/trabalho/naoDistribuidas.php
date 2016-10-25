<?php
$this->menu = [];
$this->menu[] = ['Voltar',$this->createUrl('/trabalho/ver',[
	'id'=>$trabalho->id,])];

?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=CHtml::link($trabalho->nome,$this->createUrl('/trabalho/ver',[
		'id'=>$trabalho->id,
	]))?>
	&raquo; Não exportadas
</h3>
<ul>
<?php foreach($naoDistribuidas as $nd): ?>
	<?php
	$output = json_decode($nd->resultado->conteudo);
	?>
	<li>
		<hr>
		<?=CHtml::link($nd->nome,$this->createUrl('/distribuido/ver',[
			'id' => $nd->id,
		]));?> 
		| <?=CHtml::link("Aplicar máscara com tolerância",$this->createUrl('/Forca/index',[
			'id'=>$nd->id,
		]));?>
		| <?=CHtml::link("Informar âncoras manualmente",$this->createUrl('/reprocessa/ancora',['id'=>$nd->id,]));?>
		<?php $linkImg = str_replace('repositorios', '..', $trabalho->sourceDir).'/'.$nd->nome; ?>
		<?=CHtml::image($linkImg,'',[
			'class'=>'zoom',
			'data-zoom-imag'=>$linkImg,
		]);?>

	</li>
<?php endforeach; ?>
</ul>
<script type="text/javascript">
$(".zoom").elevateZoom({
  zoomType: "lens",
  lensShape : "round",
  lensSize: 200,
  // scrollZoom: true,
});
</script>