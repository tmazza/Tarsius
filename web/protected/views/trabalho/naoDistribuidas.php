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
	$output = json_decode($nd->output);
	?>
	<li>
		<hr>
		<?=$nd->nome;?> | <?=CHtml::link("Informar âncoras",$this->createUrl('/reprocessa/ancora',[
			'id'=>$nd->id,
		]));?>
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