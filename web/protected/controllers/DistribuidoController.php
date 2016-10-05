<?php
class DistribuidoController extends BaseController {

	public function actionVer($id,$renovar=false){
		include_once __DIR__ . '/../../../src/Helper.php';
		$model = Distribuido::model()->findByPk((int)$id);
		Yii::app()->clientScript->registerScriptFile($this->wb.'/jquery.elevatezoom.min.js');
		try {
			if(is_null($model)){
				throw new Exception("Registro finalizado ID:'$id' não encontrado.", 3);
			} else {
				$debugImage = $this->getDebugImage($model,$renovar);
				$this->render('ver',[
					'model'=>$model,
					'debugImage'=>$debugImage,
				]);
			}
		} catch(Exception $e){
			$this->render('verComErro',[
				'model'=>$model,
				'msg' => $e->getMessage(),
			]);
		}

	}

	private function getDebugImage($dist,$renovar=false){		
		$baseDir	 = __DIR__ . '/../../../data/runtime/trab-'.$dist->trabalho->id;
		$file = $dist->nome;

		$imgDir = $baseDir.'/img/';
		$reviewImage = $imgDir.substr($file,0,-4) . '.png';

		if(!file_exists($reviewImage) || $renovar){
			
			# cria diretorio para imagens de debug
			if(!is_dir($imgDir)) mkdir($imgDir,0777);
			# busca json de debug
			$jsonFile = $baseDir.'/file/' . $file . '.json';
			$output = json_decode($dist->resultado->conteudo,true);

			if(is_string($output))
				throw new Exception($output, 2);

			# carrega imagem original
			$originalFile = $dist->trabalho->sourceDir.'/'.$dist->nome;
			if(!file_exists($originalFile)) throw new Exception("Arquivo '{originalFile}' não encontrado.", 1);
			$original = imagecreatefromjpeg($originalFile);

			$strTempalte = file_get_contents(Yii::app()->params['templatesDir'] . '/' . $dist->trabalho->template . '/template.json');
			$template = json_decode($strTempalte,true);
			$preenchimentoMinimo = $dist->trabalho->taxaPreenchimento;
			$escala = $output['escala'];
			$regioes = $output['regioes'];
			# Desenha formas nas posições avaliadas
			foreach ($regioes as $r) {
			  if($r[0] == 0) { # tipo elipse
			    
			    $w = $escala * $template['elpLargura'] ;
			    $h = $escala * $template['elpAltura'];
			    list($x,$y) = Helper::rotaciona([$r[2],$r[3]],$output['ancoras'][1],0);

			    if($r[1] > $preenchimentoMinimo) { # todo: adicionar taxa de PREENCHIMENTO_MINIMO no template!
			      imagefilledellipse($original,$x,$y,$w,$h, imagecolorallocatealpha($original,255,255,0,75));
			    } else {
			      imageellipse($original,$x,$y,$w,$h, imagecolorallocate($original, 255,0,255));
			    }
			  } else {
			    ///throw new Exception("Tipo de região {$r[0]} desconhecido.", 1);
			  }
			}

			imagepng($original,$reviewImage);
		}
		return  Yii::app()->baseUrl . '/../data/runtime/trab-'.$dist->trabalho->id.'/img/'.substr($file,0,-4) . '.png';
	}


}