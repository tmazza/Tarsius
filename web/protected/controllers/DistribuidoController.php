<?php
class DistribuidoController extends BaseController {

	public function actionVer($id){
		include_once __DIR__ . '/../../../src/Helper.php';
		$model = Distribuido::model()->findByPk((int)$id);
		$this->render('ver',[
			'model'=>$model,
			'debugImage'=>$this->getDebugImage($model),
		]);
	}

	private function getDebugImage($dist){
		$baseDir = __DIR__ . '/../../../data/runtime/trab-'.$dist->trabalho->id;
		$file = $dist->nome;

		$imgDir = $baseDir.'/img/';
		$reviewImage = $imgDir.substr($file,0,-9) . '.png';

		

		if(!file_exists($reviewImage)){
			# cria diretorio para imagens de debug
			if(!is_dir($imgDir)) mkdir($imgDir,0777);
			# busca json de debug
			$jsonFile = $baseDir.'/file/' . $file . '.json';
			$handle = fopen($jsonFile,'r');
			$json = fread($handle,filesize ($jsonFile));
			fclose($handle);
			$output = json_decode($json,true);
			# carrega imagem original
			$originalFile = $dist->trabalho->sourceDir.'/'.$dist->nome;
			if(!file_exists($originalFile)) throw new Exception("Arquivo '{originalFile}' não encontrado.", 1);
			$original = imagecreatefromjpeg($originalFile);

			$template = include Yii::app()->params['templatesDir'] . '/' . $dist->trabalho->template . '.php';
			$preenchimentoMinimo = $dist->trabalho->taxaPreenchimento;
			$escala = $output['escala'];
			$regioes = $output['regioes'];

			# Desenha formas nas posições avaliadas
			foreach ($regioes as $r) {
			  if($r[0] == 0) { # tipo elipse
			    
			    $w = $escala * $template['elpLargura'] ;
			    $h = $escala * $template['elpAltura'];
			    list($x,$y) = Helper::rotaciona([$r[2],$r[3]],$output['ancoras'][1],$output['rotacao']);

			    if($r[1] > $preenchimentoMinimo) { # todo: adicionar taxa de PREENCHIMENTO_MINIMO no template!
			      imagefilledellipse($original,$x,$y,$w,$h, imagecolorallocatealpha($original,255,255,0,75));
			    } else {
			      imageellipse($original,$x,$y,$w,$h, imagecolorallocate($original, 255,0,255));
			    }

			  } else {
			    throw new Exception("Tipo de região {$r[0]} desconhecido.", 1);
			  }
			}
			imagepng($original,$reviewImage);
		}

		return 'data:image/png;base64,' . base64_encode(file_get_contents($reviewImage));
		 
	}


}