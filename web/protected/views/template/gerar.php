<h2><?=$template?></h2>
<br>
<table class="uk-table" style="max-width:320px">
  <tr>
    <td class="state0" style="color:white;width:24px;text-align:center;">E</td><td>região com elipses</td>
  </tr>
  <tr>
    <td class="state1" style="color:white;width:24px;text-align:center;">B</td><td>região com código de barras</td>
  </tr>
  <tr>
    <td class="co">Esc</td><td>Desfaz seleção</td>
  </tr>
</table>
<div onkeypress="" onload="updateView();">
  <canvas id="myCanvas" style='border:1px solid red;margin:0px auto!important;display: block!important;overflow: hidden!important;'>Browser não suporta canvas!</canvas>
  <div style="margin-bottom:200px"></div>
  <div id='pontos'></div>
  <div class='bottom-bar'>
    <form action="<?=$this->createUrl('/template/processar',[
      'template'=>$template,
    ])?>" method="post">
      <div class="uk-button-group">
        <?=CHtml::link("Cancelar",$this->createUrl('/template/index'),[
          'class'=>'uk-button uk-button-primary',
        ]); ?>
        <button type='button' onclick="undo();" class='uk-button'>Desfazer seleção</button>
        <div style="padding:0px;" id='state' class='uk-button estado'></div>
        <button type="submit" class="uk-button uk-button-success">Gerar template</button>
      </div>
      <input type="hidden" name="pontos" id="to-send" />
    </form>
  </div>
</div>
<div class="preview"></div>

<script>
$('body').keypress(function(event){
  changeState(event);
});
<?php if($blocos): ?>
  var pontos = JSON.parse('<?=$blocos;?>');
  var contadorBlocos = <?=$qtdBlocos;?> 
<?php else: ?>
  var pontos = {};
  var contadorBlocos = 0;
<?php endif; ?>
var aberturaPonto = {}; 
var emEdicao = false;

img = new Image();
img.src = '<?=$urlImage;?>';

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
modalActive = false;

function changeState(e){
  if (!modalActive) {

    var keynum;
    if(window.event){ // IE
      keynum = e.keyCode;
    } else if(e.which){ // Netscape/Firefox/Opera
      keynum = e.which;
    }
    char = String.fromCharCode(keynum);
    lastState = state;
    if(char == 'e'){ 
      state = 0;
    } else if(char == 'o') {
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
}

function pick(event) {


  var elem = $('#myCanvas').position();
  var x = event.layerX - elem.left;
  var y = event.layerY - elem.top;

  if(!open) { // Verifica se segundo ponto está acima e a direita do primeiro
    fechamentoPonto = {x: x,y: y,state:state};
    if(fechamentoPonto['x'] < aberturaPonto['x'] || fechamentoPonto['y'] > aberturaPonto['y']){
      alert('Faça a seleção de uma das diagonaisl do retângulo. Marque primeiro o ponto inferior esquerdo e depois o ponto superior direito.');
      return false;
    }
    pontos[contadorBlocos] = {
      'p1' : aberturaPonto,
      'p2': fechamentoPonto,
      'tipo' : state,
    };
    emEdicao = contadorBlocos;
    abreEdicao();
    contadorBlocos++;
  }

  aberturaPonto = {x: x,y: y,state:state};
  dc(x,y);
  if(!open) { checkSwitch();  }
  if(!open){
    context.save();
    bloco = pontos[contadorBlocos-1];
    p1 = bloco['p1'];
    p2 = bloco['p2'];
    color = getCor();
    context.globalAlpha=0.25;
    context.fillRect(p2['x'],p2['y'] - (p2['y'] - p1['y']),p1['x'] - p2['x'],p2['y'] - p1['y']);
    context.strokeStyle = color;
    context.stroke();
    context.restore();
  }

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
  $.each(pontos, function(k,v){
    content += 'Bloco ' + (parseInt(k)+1) + ' | ';
    content += ' <a onclick="editarBloco(' + k + ')">Editar</a>';
    content += '<br>';
  });
  $('#pontos').html(content);
  $('#to-send').val(JSON.stringify(pontos));
  atualizaEstado();
}

function atualizaEstado(){
  txtState = 'Elipses';

  if(state == 1) txtState = 'OCR';
  
  content = '<div class="state state'+state+'">' + txtState;
  
  if(!open) content += ' (em aberto)';

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
  p1 = aberturaPonto;
  if(p1 !== undefined && !open){
    x = p1['x'];
    y = e.pageY;
    w = e.pageX - x;
    h = (p1['y'] + $('#myCanvas').position().top) - y;

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
  initDraw();

  $('#edicao-bloco').on({
      'show.uk.modal': function(){
          modalActive = true;
      },
      'hide.uk.modal': function(){
          modalActive = false;
      }
  });
});


function abreEdicao(){
  bloco = pontos[emEdicao];
  $('.bloco-cfg').each(function( index ) {
    name = $(this).attr('name');
    if(bloco.hasOwnProperty(name)){
      $(this).val(bloco[name])
    } else {
      $(this).val($(this).attr('data-default'));
    }
  });
  $('.state-config').show();
  if(state == 0) $('.not-state-0').hide();
  if(state == 1) $('.not-state-1').hide();
  
  UIkit.modal('#edicao-bloco').show();
}

function gravaEdicao(){
  bloco = pontos[emEdicao];
  $('.bloco-cfg').each(function( index ) {
    val = $(this).val();
    if(val.length > 0){
      name = $(this).attr('name');
      bloco[name] = val;
    }    
  });
  pontos[emEdicao] = bloco;
  $('#to-send').val(JSON.stringify(pontos));
  UIkit.modal('#edicao-bloco').hide();
}

function editarBloco(numBloco){
  emEdicao = numBloco;
  abreEdicao();
}

function initDraw(){
setTimeout(function(){ 
  
  $.each(pontos, function(k,v){
    p1 = v['p1']
    p2 = v['p2']
    state = v['tipo']
    context.save();
    bloco = pontos[contadorBlocos-1];
    color = getCor();
    context.globalAlpha=0.25;
    context.fillRect(p2['x'],p2['y'] - (p2['y'] - p1['y']),p1['x'] - p2['x'],p2['y'] - p1['y']);
    context.strokeStyle = color;
    context.stroke();
    context.restore();
  });

  updateView();

},1000);
}

</script>

<div id="edicao-bloco" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <a class="uk-modal-close uk-close"></a>
        <div class="uk-modal-header">
          <h3>Especificação do bloco</h3>
        </div>
        <hr>
        <form class="uk-form uk-form-horizontal">
          <div class="uk-form-row state-config not-state-1">
            <label class="uk-form-label">Colunas por linha</label>
            <input name='colunasPorLinha' class="bloco-cfg" data-default='15'/><br>
          </div>
          <div class="uk-form-row state-config not-state-1">
            <label class="uk-form-label">Agrupar colunas de cada linha em</label>
            <input name='agrupaObjetos' class="bloco-cfg" data-default='5'/><br>
          </div>
          <div class="uk-form-row state-config not-state-1">
            <label class="uk-form-label">Área mínima e máxima para considerar a região um objeto</label>
             entre
             <input name='minArea'  class="bloco-cfg"  data-default='300' style='width:80px'/>
             e
            <input name='maxArea'  class="bloco-cfg"  data-default='3000' style='width:80px'/><br>
          </div>
          <div class="uk-form-row">
            <label class="uk-form-label">Identificar ùnico<span class="not-state-1"> para cada região do bloco</span></label>
            <textarea name='id' style='width:600px;height:140px'; class="bloco-cfg"  id='t-id' data-default="function($b,$l,$o) {
  $idQuestao = str_pad($b*20 + $l+1,3,'0',STR_PAD_LEFT);
  return 'e-'.$idQuestao.'-'.($o+1);
}"></textarea>
          </div>
          <div class="uk-form-row state-config not-state-1">
            <label class="uk-form-label">Valor caso elipse esteja preenchida</label>
            <textarea name='casoTrue' style='width:600px;height:200px'; class="bloco-cfg"   id='t-ct' data-default="function($b,$l,$o) { 
  switch ($o){
    case 0: return 'A';
    case 1: return 'B';
    case 2: return 'C';
    case 3: return 'D';
    case 4: return 'E';
  }
}"></textarea><br>
          </div>
          <div class="uk-form-row state-config not-state-1">
            <label class="uk-form-label">Valor caso elipse <b>não</b> esteja preenchida</label>
            <textarea name='casoFalse'  class="bloco-cfg" data-default="W"   id='t-cf' style='width:600px;height:140px';></textarea>
          </div>
        </form>
        <br>
        <button class="uk-button uk-button-primary" onclick="gravaEdicao()">Salvar edição</button>
    </div>
</div>
<!-- 
<link rel="stylesheet" href="<?=$this->wb;?>/codemirror/lib/codemirror.css">
<script src="<?=$this->wb;?>/codemirror/lib/codemirror.js"></script>
<script>
  var modoPHP = {
    lineNumbers: true,
    matchBrackets: true,
    mode: "application/x-httpd-php",
    indentUnit: 4,
    indentWithTabs: true,
    enterMode: "keep",
    tabMode: "shift"
  };
  var editorID = CodeMirror.fromTextArea(document.getElementById("t-id"), modoPHP);
  var editorCT = CodeMirror.fromTextArea(document.getElementById("t-ct"), modoPHP);
  var editorCF = CodeMirror.fromTextArea(document.getElementById("t-cf"), modoPHP);
</script> -->





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
.state0 { background: #f22; }
.state1 { background: #00a; }
.state2 { background: #ff0; color: black; }
table td { border:2px solid #03a9f4; }
.container { width: 100%; }
.bottom-bar {
  font-size:25px;
  height:40px;
  position:fixed;
  bottom:0px;
  right:0px;
  padding-right: 20px;
}
.estado {
  text-align: center;
  font-size: 23px;
  width:300px;
  color: white;
}
#pontos {
  padding: 12px;
  position:fixed;
  right: 0px;
  bottom: 40px;
  font-family: monospace;
  font-size: 18px;  
  display: block;
  width: 609px;
  height: 100px;
  overflow: hidden;
  overflow-y: auto;
  background: #ddd;
  opacity: 0.9;
  margin-right: 20px;
}
#myCanvas {
    max-width: none;}
-->
</style>