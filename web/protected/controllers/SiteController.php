<?php
class SiteController extends BaseController {


  public function actionIndex(){
    $this->render('index');
  }

  public function actionError(){
    echo 'erro';
  }


}
