<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/finalizadas',['id'=>$model->trabalho->id])],
];
?>
<h3><?=$model->nome?></h3>
<hr>


	<a href="#!" onclick="$(this).next().slideToggle();">Ver resultado em texto</a>
	<div style="display: none;">
		<?php $output = json_decode($model->resultado->conteudo,true); ?>
		<h4>Taxa de preenchimento por região</h4>
		<?php
		if(isset($output['saidaFormatada'])){
			echo '<pre>';
			foreach ($output['regioes'] as $k => $r){
				if($r[0] == 0){
				 	echo $k . ' | ' . $r[0] . ' | ' . number_format(100*$r[1],2) . '%<br>';
				} else {
				 	echo $k . ' | ' . $r[0] . '<br>';
				}
			}
			echo '</pre>';
		}
		?>
		<hr>
		<h4>Arquivo completo</h4>
		<?php
		echo '<pre>';
		print_r($output);
		echo '</pre>';
		?>
	</div>
<?php if(!$model->exportado): ?>
	<?=CHtml::link('Exportar',$this->createUrl('/trabalho/forcaExport',[
		'id'=>$model->id,
	]),[
		'class'=>'uk-button uk-button-primary uk-button-small uk-float-right'
	])?>
<?php endif; ?>

<!-- <img id='main' src="<?=$debugImage;?>" style="width:100%;" /> -->
<img id="zoom_01" src="<?=$debugImage;?>" data-zoom-image="<?=$debugImage;?>"/>


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