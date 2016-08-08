<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/finalizadas',['id'=>$model->trabalho->id])],
];
?>
<h3><?=$model->nome?></h3>
<hr>
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