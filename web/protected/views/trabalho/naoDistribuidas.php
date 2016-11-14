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

<div class="uk-text-right">
	Quantidade de imagens por página:
	<?php
	$urlBase = function($pageSize) use($trabalho) { 
		return $this->createUrl('/trabalho/naoDistribuidas',[
			'id'=>$trabalho->id,
			'pageSize'=>$pageSize,
		]);
	}; 
	echo CHtml::dropDownList('pageSize',(int)@$_GET['pageSize'],[
		$urlBase(8) => 8,
		$urlBase(16) => 16,
		$urlBase(32) => 32,
		$urlBase(64) => 64,
		$urlBase(128) => 128,
		$urlBase(256) => 256,
		$urlBase(512) => 512,
	],[
		'onchange' => "window.location.href=this.options[this.selectedIndex].value",
	]);?>
</div>


<ul>
<?php foreach($naoDistribuidas as $nd): ?>
	<?php
	$output = json_decode($nd->resultado->conteudo);
	?>
	<li>
		<hr>
		<ul>
			<li>
				<?=CHtml::link("Aplicar máscara com tolerância",$this->createUrl('/Forca/index',[
					'id'=>$nd->id,
				]));?>
			</li>
			<li>
			<?=CHtml::link("Informar âncoras manualmente",$this->createUrl('/reprocessa/ancora',['id'=>$nd->id,]));?>
			</li>
		</ul>
		<br>
		<div class="uk-grid">
			<div class="uk-width-1-2">
				<?php $linkImg = str_replace('repositorios', '..', $trabalho->sourceDir).'/'.$nd->nome; ?>
				<?=CHtml::image($linkImg,'',[
					'class'=>'zoom',
					'data-zoom-imag'=>$linkImg,
					'style'=>'width:320px',
				]);?>
			</div>
			<div class="uk-width-1-2">
				<?php
				try {
					if(is_null($nd)){
						throw new Exception("Registro finalizado ID:'$id' não encontrado.", 3);
					} else {
						$debugImage = DistribuidoController::getDebugImage($nd,1);
						$this->renderPartial('/distribuido/ver',[
							'model'=>$nd,
							'debugImage'=>$debugImage,
						]);
					}
				} catch(Exception $e){
					echo $e->getMessage();
				}
				?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)) ?>
<script type="text/javascript">
$(".zoom").elevateZoom({
  zoomType: "lens",
  lensShape : "round",
  lensSize: 200,
  // scrollZoom: true,
});
</script>