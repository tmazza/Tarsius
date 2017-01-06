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

<button onclick="$('.checkbox').prop('checked', 'checked');">Check All</button>

<?=CHtml::beginForm('','POST',[
	'id'=>'emMassa',
]);?>
<ul>
	<?php foreach($naoDistribuidas as $nd): ?>
		<?php
		$output = json_decode($nd->resultado->conteudo);
		?>
		<li>
			<hr>
			<input class="checkbox" type="checkbox" name="folha[]" value="<?=$nd->id;?>" />
			<br><br>
			<ul>
				<li>
					<?=CHtml::link("Aplicar máscara com tolerância",$this->createUrl('/Forca/index',[
						'id'=>$nd->id,
					]));?>
				</li>
				<li>
					<?=CHtml::link("Informar âncoras manualmente",$this->createUrl('/reprocessa/ancora',['id'=>$nd->id,]));?>
				</li>
				<li>
					Rotacionar: 
					<?=CHtml::ajaxLink("&#8634;",$this->createUrl('/reprocessa/rotacionar',[
						'id'=>$nd->id,
						'angulo'=>270,
					]),[
						'update' => '#img-' . $nd->id,
					],[
						'class' => 'uk-button uk-button-link',
						'style' => 'font-size: 20px',
					]);?> 
					<?=CHtml::ajaxLink("&#8645;",$this->createUrl('/reprocessa/rotacionar',[
						'id'=>$nd->id,
						'angulo'=>180,
					]),[
						'update' => '#img-' . $nd->id,
					],[
						'class' => 'uk-button uk-button-link',
						'style' => 'font-size: 20px',
					]);?>
					<?=CHtml::ajaxLink("&#8635;",$this->createUrl('/reprocessa/rotacionar',[
						'id'=>$nd->id,
						'angulo'=>90,
					]),[
						'update' => '#img-' . $nd->id,
					],[
						'class' => 'uk-button uk-button-link',
						'style' => 'font-size: 20px',
					]);?>
				</li>

			</ul>
			<br>
			<div class="uk-grid">
				<div class="uk-width-1-2" id='img-<?=$nd->id?>'>
					<?php $linkImg = $trabalho->urlImagens.'/'.$nd->nome; ?>
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
<div class="uk-alert">
	<b>Reprocessar com: </b>
	<br>
	Preenchimento mínimo elipses
	<input type="text" name="minMatch" value="0.75" /> 
	<br>
	<label for='checkbox-validartemplate'>
	<input type="checkbox" name="validaTemplate" id='checkbox-validartemplate' checked="" />
	Validar template
	</label>
</div>
OU<br>
<div class="uk-alert">
	<br>
	<label>
		<input type="radio" name="descartar" value="1" />
		Descartar
		<small>Não será reprocessado</small>
	</label>
	<br>
	<label>
		<input type="radio" name="descartar" value="2" />
		Reler
		<small>Será reprocessado</small>
	</label>
</div>
<br><br>
<br>

<?=CHtml::endForm();?>
<button onclick="aplicaEmMassa('<?=$this->createUrl('/forca/EmMassa');?>')">Aplicar</button>

<br>
<br>
<hr>
<?php
$this->widget('CLinkPager', array(
    'pages' => $pages,
));
?>

<script type="text/javascript">

function aplicaEmMassa(url)
{
	$('#emMassa').attr('action',url);
	$('#emMassa').submit();

}

$(".zoom").elevateZoom({
  zoomType: "lens",
  lensShape : "round",
  lensSize: 200,
  // scrollZoom: true,
});
</script>