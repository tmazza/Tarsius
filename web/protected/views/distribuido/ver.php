<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/finalizadas',['id'=>$model->trabalho->id])],
];
?>
<h3><?=$model->nome?></h3>
<hr>

<?php if(!$model->exportado): ?>
	Saída: 
	<?php
	$output = json_decode($model->output,true);
	echo '<pre>';
	print_r($output['saidaFormatada']);
	echo '</pre>';
	?>
	<?=CHtml::link('Exportar',$this->createUrl('/trabalho/forcaExport',[
		'id'=>$model->id,
	]),[
		'class'=>'uk-button uk-button-primary uk-button-small'
	])?>
	<br>
	<br>
<?php endif; ?>

<!-- <img id='main' src="<?=$debugImage;?>" style="width:100%;" /> -->
<img id="zoom_01" src="<?=$debugImage;?>" data-zoom-image="<?=$debugImage;?>"/>

<?php if(!$model->exportado): ?>
	<hr>
	<?php
	if(isset($output['saidaFormatada'])){
		echo '<pre>';
		foreach ($output['regioes'] as $k => $r)
			echo $k . ' | ' . $r[0] . ' | ' . number_format(100*$r[1],2) . '%<br>';
		echo '</pre>';
	}
	?>
	<hr>
<?php endif; ?>

<script type="text/javascript">
$("#zoom_01").elevateZoom({
  zoomType: "lens",
  lensShape : "round",
  lensSize: 200,
  // scrollZoom: true,
});
</script>

<style type="text/css">
	
	.zoomLens {
		position: fixed;
	}
</style>