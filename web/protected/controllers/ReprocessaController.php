<?php
include_once(Yii::getPathOfAlias('webroot') . '/../src/Image.php');

class ReprocessaController extends BaseController {

	public function actionAncora($id){
		$this->layout = '//layouts/base';
		$model = Distribuido::model()->findByPk((int)$id);
		
		if(isset($_POST['pontos'])){
			$this->aplicaMascara($model,json_decode($_POST['pontos'],true));
		}

		$this->render('ancora',[
			'model'=>$model,
			'urlImage'=> str_replace('/repositorios','',$model->trabalho->sourceDir).'/'.$model->nome,
		]);
	}

	private function aplicaMascara($model,$pontos){
		if(count($pontos) == 4){
			$ok = true;
			try {
				$image = new Image($model->trabalho->template,$model->trabalho->taxaPreenchimento);
				$image->validaTemplate = false;
				$image->execComAncoras($model->trabalho->sourceDir.'/'.$model->nome,$pontos,300);
				$model->resultado->conteudo = json_encode($image->output);
				$model->resultado->update(['conteudo']);
			} catch (Exception $e) {
				$ok = false;
				$msg = $e->getMessage();
			}	

			if($ok){
				$this->redirect($this->createUrl('/distribuido/ver',[
					'id'=>$model->id,
					'renovar'=>1,
				]));
			} else {
				$this->redirect($this->createUrl('/reprocessa/ancora',[
					'id'=>$model->id,
					'msg'=>$msg . ' | com ',
				]));
			}
		} else {
			echo 'Qtd de pontos inválida';
		}
	}

}