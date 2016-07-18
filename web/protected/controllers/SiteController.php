<?php
class SiteController extends CController {


  public function actionIndex(){
    $trabalhos = Trabalho::model()->findAll();
    $this->render('index',[
      'trabalhos'=>$trabalhos,
    ]);
  }

}
