<?php
class SiteController extends BaseController {


  public function actionIndex(){
    $this->render('index');
  }

  public function actionError(){
    echo '<pre>';
    print_r(Yii::app()->errorHandler);
    echo '</pre>';
  }

}
