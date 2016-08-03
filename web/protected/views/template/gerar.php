<br>
<div onkeypress="" onload="updateView();">
  <canvas id="myCanvas" width="20" height="20" style='border:1px solid red;'>Browser não suporta canvas!</canvas>

  <div class='bottom-bar'>
    <div id='pontos'></div>
    <form action="<?=$this->createUrl('default/applyCut')?>" method="post">
      <button type='button' onclick="undo();" class='btn black waves-effect waves-red'>Desfazer</button>
      <div id='state' class='estado'></div>
      <input type="hidden" name="pontos" id="to-send" />
    </form>
  </div>
</div>
<div class="preview"></div>

<?php $urlImage = Yii::app()->baseUrl . '/../data/gerarTemplate/a.jpg'; ?>
<script>
$('body').keypress(function(event){
  changeState(event);
});

var pontos = [];
img = new Image();
img.src = '<?=$urlImage;?>';
// img.src = 'http://www.almanaque.cnt.br/paradoxoPK05.jpg';

// Get a reference to the element.
var elem = document.getElementById('myCanvas');

if (elem && elem.getContext) {
	var context = elem.getContext('2d');
	if (context) {
    img.onload = function() {
        elem.setAttribute('width',img.width);
        elem.setAttribute('height',img.height);
          context.drawImage(img, 0, 0);
        };
	}
}
elem.addEventListener('click', pick);
mustClose = false;
state = 0;
lastState = false;
open = true;

function changeState(e){
    var keynum;

    if(window.event){ // IE
      keynum = e.keyCode;
    }else
        if(e.which){ // Netscape/Firefox/Opera
        keynum = e.which;
    }
    char = String.fromCharCode(keynum);
    lastState = state;
    if(char == 'q'){ 
      state = 0;
    } else if(char == 'f') {
      state = 1;
    } else if(e.keyCode === 27) {
      undo();
    }
    if(!open && lastState != state){
      alert("Não pode mudar de tipo, pois outro esta aberto.");
      state = lastState;
    }
    atualizaEstado();
}

function pick(event) {
  var elem = $('#myCanvas').position();
  var x = event.layerX - elem.left;
  var y = event.layerY - elem.top;


  if(!open) { // Verifica se segundo ponto está acima e a direita do primeiro
    ultimoPonto = pontos[pontos.length-1];
    if(x < ultimoPonto['x'] || y > ultimoPonto['y']){
      alert('Faça a seleção de uma das diagonaisl do retângulo. Marque primeiro o ponto inferior esquerdo e depois o ponto superior direito.');
      return false;
    }
  }

  pontos.push({x: x,y: y,state:state});
  dc(x,y);

  if(!open) { // close
    checkSwitch();
  }
  context.save();
  if(!open){
    p1 = pontos[pontos.length - 1];
    p2 = pontos[pontos.length - 2];
    color = getCor();
    context.globalAlpha=0.25;
    context.fillRect(p2['x'],p2['y'] - (p2['y'] - p1['y']),p1['x'] - p2['x'],p2['y'] - p1['y']);
    context.strokeStyle = color;
    context.stroke();
  }
  context.restore();
  open = !open;
  updateView();
  cPush();
}

function undo(){
  if(cStep > 0){
    cUndo();
    p = pontos.pop();
    updateView();
    open = !open;
  }
}

function checkSwitch(){}

// INterface
function dc(x,y,w){
  color = getCor(w);
  context.beginPath();
  context.arc(x, y, 5, 0, 2 * Math.PI, false);
  context.fillStyle = color;
  context.fill();
}

function getCor(w){
  if(w){
    color = '#fff';
  } else {
    if(state == 0){
      color = '#f00';
    } else if(state == 1) {
      color = '#00f';
    }
  }
  return color;
}

function updateView(){
  content = '';
  for(i=pontos.length-1;i>=0;i--){
    content += '(' + pontos[i]['x'] + ',' + pontos[i]['y'] + ')';
  }
//  $('#pontos').html(content);
  $('#to-send').val(JSON.stringify(pontos));
  atualizaEstado();
}

function atualizaEstado(){

  txtState = 'Questão';
  if(state == 1){
    txtState = 'Compartilhado Desconexo';
  } else if(state == 2){
    txtState = 'Imagem';
  }
  content = '<div class="state state'+state+'">' + txtState;

  if(!open){
    content += ' (em aberto)';
  }

  content += '</div>';

  $("#state").html(content);

}
// UNDO/REDO
 var cPushArray = new Array();
 var cStep = -1;
 var ctx;

 function cPush() {
     cStep++;
     if (cStep < cPushArray.length) { cPushArray.length = cStep; }
     cPushArray.push(document.getElementById('myCanvas').toDataURL());
 }
 function cUndo() {
   if(cStep > 0){
     cStep--;
     var canvasPic = new Image();
     canvasPic.src = cPushArray[cStep];
     canvasPic.onload = function () { context.drawImage(canvasPic, 0, 0); }
   }
 }
// function cRedo() {
//     if (cStep < cPushArray.length-1) {
//         cStep++;
//         var canvasPic = new Image();
//         canvasPic.src = cPushArray[cStep];
//         canvasPic.onload = function () { context.drawImage(canvasPic, 0, 0); }
//     }
// }

$(window).mousemove(function(e){
  p1 = pontos[pontos.length-1];
  if(p1 !== undefined && !open){
    x = p1['x'];
    y = e.pageY;
    w = e.pageX - x;
    h = (p1['y'] + $('#myCanvas').position().top) - y;

    console.log(w);
    console.log(h);

    marginCanvas = $('#myCanvas').position().left;

    $('.preview').css({
        'top' : y+'px',
        'left' : (x+marginCanvas)+'px',
        'width' : (w-marginCanvas)+'px',
        'height' : (h)+'px',
        'background' : getCor(false),
    });
  } else {
    $('.preview').css({
        'width' : '0px',
        'height' : '0px',
    });
  }
});

$(document).ready(function(){
  atualizaEstado();
});
</script>
<style>
<!--
.preview {
  position: absolute;
  z-index: 100;
  background: black;
  top: 0px;
  left: 0px;
  width: 0px;
  height: 0px;
  color: red;
  opacity: 0.6;
}
.state0 { background: red; }
.state1 { background: blue; }
table td { border:2px solid #03a9f4; }
.container { width: 100%; }
.bottom-bar {
  font-size:25px;
  height:40px;
  position:fixed;
  bottom:0px;
  right:0px;
  padding-bottom: 60px;
  padding-right: 40px;
}
.estado {
  text-align: center;
  font-size: 23px;
  display:inline-block;
  width:300px;
  color: white;
}
-->
</style>