<div style="margin: 0 auto;width: 3000px;">
	<h3>Reprocessar imagem <?=$model->nome?></h3>

	<canvas id="myCanvas" width="20" height="20" style='border:1px solid red;margin:0px auto!important;display: block;'>
	Browser não suporta canvas!
	</canvas>
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

  if(pontos.length > 1)
  	geraImagemPreview();
}

init();
canvas.onclick = addPonto;


function geraImagemPreview(){
	$.ajax({
		type:'POST',		
		url: '<?=$this->createUrl('/reprocessa/preview');?>',
		data: {pontos:pontos,dist:<?=$model->id?>},
	});	
}
</script>