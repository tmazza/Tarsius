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
					if(is_array($r[2])){
					 	echo $k . ' | ' . $r[0] . '<br>';
					} else {
					 	echo $k . ' | ' . $r[0] . ' | ' . number_format(100*$r[1],2) . '%<br>';
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

<?php $zoomId = 'zoom_'.$model->id;?>
<img id="<?=$zoomId?>" src="<?=$debugImage;?>" data-zoom-image="<?=$debugImage;?>"/>


<script type="text/javascript">
$("#<?=$zoomId?>").elevateZoom({
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