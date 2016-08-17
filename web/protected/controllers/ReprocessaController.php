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
				$image->execComAncoras($model->trabalho->sourceDir.'/'.$model->nome,$pontos);
				$model->output = json_encode($image->output);
				$model->update(['output']);
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
					'msg'=>$msg . ' | com ' . $minMatch,
				]));
			}
		} else {
			echo 'Qtd de pontos inválida';
		}
	}

}