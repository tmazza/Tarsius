<?php
class SiteController extends BaseController {


  public function actionIndex(){
    $this->render('index', [
        'trabalhos' => Trabalho::model()->findAll("status = ". Trabalho::statusExecutando 
                . " OR status = " . Trabalho::statusDeveParar),
    ]);
  }

  public function actionError(){
    echo '<pre>';
    print_r(Yii::app()->errorHandler);
    echo '</pre>';
  }

}
