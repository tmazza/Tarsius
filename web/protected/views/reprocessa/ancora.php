<h3>Reprocessar imagem <?=$model->nome?></h3>

<canvas id="myCanvas" style='border:1px solid red;max-width: none;'>
Browser não suporta canvas!
</canvas>

<div class="uk-panel uk-panel-box">
<?=CHtml::beginForm();?>
	<?=CHtml::textField('pontos','',[
		'id'=>'pontos',
		'style'=>'width:600px',
	]);?>
<?=CHtml::submitButton('Aplicar máscara');?>
<?=CHtml::endForm();?>
</div>

<script>

var canvas;
var ctx;
var dragok = false;

var pontos = [];

img = new Image();
img.src = '<?=$urlImage;?>';

function init() {
	canvas = document.getElementById("myCanvas");
	if (canvas && canvas.getContext) {
		ctx = canvas.getContext('2d');
		if (ctx) {
			img.onload = function() {
		        canvas.setAttribute('width',img.width);
		        canvas.setAttribute('height',img.height);
		          ctx.drawImage(img, 0, 0);
		    };
		}
	}
}

function rect(x,y,w,h) {
 ctx.beginPath();
 ctx.rect(x,y,w,h);
 ctx.closePath();
 ctx.fill();
}

function addPonto(e){
  var elem = $('#myCanvas').position();
  x = e.layerX - elem.left;
  y = e.layerY - elem.top;

  pontos.push({
  	'x':x,
  	'y':y,
  });
  ctx.fillStyle = "#f00";
  rect(x-5, y-5, 10, 10);

  if(pontos.length > 1){
  	$('#pontos').val(JSON.stringify(pontos));
  }
}

init();
canvas.onclick = addPonto;
</script>