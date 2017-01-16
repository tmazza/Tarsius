<?php

class ForcaController extends BaseController {

	public function actionIndex($id,$msg=false){
		if(isset($_POST['minMatch'])){
			$validaTemplate = isset($_POST['validaTemplate']) && $_POST['validaTemplate'];
			$minMatch = (float) $_POST['minMatch'];
			list($ok,$model,$msg) = $this->processar($id,$minMatch,$validaTemplate);
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
		$this->render('index',[
			'msg'=>$msg,
		]);
	}

	public function actionEmMassa()
	{
		set_time_limit(0);
		$minMatch = (float) $_POST['minMatch'];
		$validaTemplate = isset($_POST['validaTemplate']) ? (bool) $_POST['validaTemplate'] : false;
		$folhas = isset($_POST['folha']) ? $_POST['folha'] : [];

		$descartar = isset($_POST['descartar']) ? $_POST['descartar'] : false;
		if ($descartar) {
			foreach ($folhas as $f) {
				$model = Distribuido::model()->findByPk((int) $f);
				$trabalho = $model->trabalho_id;
				$nome = $model->nome;

				if($descartar == 1) { # descarta registro, não precisará ser relido
					$model->descartar();
				} else { # ficará como não distribuído, será reprocessado na distribuição
					$model->delete();
				}

				Finalizado::model()->deleteAll("trabalho_id = {$trabalho} AND nome = '{$nome}'");
			}
		} elseif(count($folhas) > 0) {
			$chuncks = array_chunk($folhas, ceil(count($folhas)/16));	
			foreach ($chuncks as $ids) {
				$this->processar($ids,$minMatch,$validaTemplate);
			}
		}
		$this->redirect(Yii::app()->request->urlReferrer);
	}

	private function processar($ids,$minMatch,$validaTemplate=true)
	{
		if(!is_array($ids)){
			$ids = [$ids];
		}
		foreach ($ids as $id) {
			$model = Distribuido::model()->findByPk((int)$id);
			$model->status = Distribuido::StatusReprocessamento;
			$ok = $model->update(['status']);
		}

		$ids = implode(' ', $ids);
		$validaTemplate = (int) $validaTemplate;
	    $cmd = 'php ' . Yii::getPathOfAlias('application') .'/tarsius processa redo';
	    $cmd .= " {$ids}";
	    $cmd .= " --minMatch={$minMatch}";
	    $cmd .= " --validaTemplate={$validaTemplate}";

	    $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');

		return [$ok,$model,$pid];
	}

}