<?php
include_once(Yii::getPathOfAlias('webroot') . '/../src/Image.php');
class ForcaController extends BaseController {

	public function actionIndex($id,$msg=false){
		if(isset($_POST['minMatch'])){
			$validaTemplate = isset($_POST['validaTemplate']) && $_POST['validaTemplate'];
			$this->processar($id,(float) $_POST['minMatch'],$validaTemplate);
		}
		$this->render('index',[
			'msg'=>$msg,
		]);
	}

	private function processar($id,$minMatch,$validaTemplate=true){
		$model = Distribuido::model()->findByPk((int)$id);
			
		$ok = true;
		try {
			$image = new Image($model->trabalho->template,$model->trabalho->taxaPreenchimento,$minMatch);
			$image->validaTemplate = $validaTemplate;
			$image->exec($model->trabalho->sourceDir.'/'.$model->nome);
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
			$this->redirect($this->createUrl('/forca/index',[
				'id'=>$model->id,
				'msg'=>$msg . ' | com ' . $minMatch,
			]));
		}
	}

}