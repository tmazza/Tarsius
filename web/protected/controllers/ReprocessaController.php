<?php
class ReprocessaController extends BaseController {

	public function actionAncora($id){

		$this->layout = '//layouts/base';
		$model = Distribuido::model()->findByPk((int)$id);
		if (substr($model->trabalho->urlImagens, -1) != '/') {
			$model->trabalho->urlImagens .= '/';
		}	
		
		if (isset($_POST['pontos'])) {
			$this->aplicaMascara($model,json_decode($_POST['pontos'],true));
		}

		$this->render('ancora',[
			'model'=>$model,
			'urlImage'=> $model->trabalho->urlImagens . $model->nome,
		]);
	}

	private function aplicaMascara($model, $pontos){
		if(count($pontos) == 4){

			try {
				$template = Yii::app()->params['templatesDir'] . '/' . $model->trabalho->template . '/template.json';
                
				if (substr($model->trabalho->sourceDir, -1) != '/') {
					$model->trabalho->sourceDir .= '/';
				}	
				
                $arquivo = $model->trabalho->sourceDir . $model->nome;

                $anchors = [
					Tarsius\Mask::ANCHOR_TOP_LEFT => array_values($pontos[0]),
					Tarsius\Mask::ANCHOR_TOP_RIGHT => array_values($pontos[1]),
					Tarsius\Mask::ANCHOR_BOTTOM_RIGHT => array_values($pontos[2]),
                	Tarsius\Mask::ANCHOR_BOTTOM_LEFT => array_values($pontos[3]),
                ];

                $form = new Tarsius\Form($arquivo, $template);
				$output = $form->evaluate($anchors);
				$model->resultado->conteudo = json_encode($output);
                $model->resultado->update(['conteudo']);

			} catch (Exception $e) {

				HView::fMsg($e->getMessage() . '<hr>' . $e->__toString());

			}	

			$this->redirect($this->createUrl('/distribuido/ver',[
				'id'=>$model->id,
				'renovar'=>1,
			]));
		} else {
			HView::fMsg('Quantidade de pontos inválida. Selecione 4 pontos. No sentido horário com o 1ª ponto no canto superior esquerdo.');
		}
	}

	public function actionRotacionar($id,$angulo=90)
	{
		$angulos = [90,180,270];
		if(in_array($angulo, $angulos)){
			$model = Distribuido::model()->findByPk((int)$id);
			$fileName = $model->trabalho->sourceDir.'/'.$model->nome;
			HImg::rotate($fileName,$angulo);
		}

		$linkImg = $model->trabalho->urlImagens.'/'.$model->nome . '?'.microtime(true);
		echo CHtml::image($linkImg,'',[
			'class'=>'zoom',
			'data-zoom-imag'=>$linkImg,
			'style'=>'width:320px',
		]);

	}

}