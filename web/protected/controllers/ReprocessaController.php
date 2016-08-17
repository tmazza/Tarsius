<?php

class ReprocessaController extends BaseController {

	public function actionAncora($id){
		$this->layout = '//layouts/base';
		$distribuido = Distribuido::model()->findByPk((int)$id);
		
		$this->render('ancora',[
			'model'=>$distribuido,
			'urlImage'=> str_replace('/repositorios','',$distribuido->trabalho->sourceDir).'/'.$distribuido->nome,
		]);
	}

	public function actionPreview(){


		# TODO: rotação!!

		if(isset($_POST['dist']) && isset($_POST['pontos'])){
			$pontos = $_POST['pontos'];
			$dist = $_POST['dist'];
			if(count($pontos) > 4){
				$model = Distribuido::model()->findByPk((int)$dist);
				$template = $this->leTemplate($model);
				$image = imagecreatefromjpeg($model->trabalho->sourceDir.'/'.$model->nome);
				$escala = 300 / 25.4;

				foreach ($template['regioes'] as $r) {
					$x = $r[1] * $escala + $pontos[0]['x'];
					$y = $r[2] * $escala + $pontos[0]['y'];
					imageellipse($image,$x,$y,4.5*$escala,2.5*$escala,imagecolorallocate($image, 255, 0, 0));
				}
				imagejpeg($image,Yii::getPathOfAlias('webroot') . '/../data/temp/preview-processa.jpg');
			} else {
				echo 'Qtd. de pontos inválida.';
			}
		}
	}

	private function leTemplate($dist){
		$strTempalte = file_get_contents(Yii::app()->params['templatesDir'] . '/' . $dist->trabalho->template . '/template.json');
		return json_decode($strTempalte,true);
	}

}