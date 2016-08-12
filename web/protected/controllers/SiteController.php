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

  public function actionTeste(){
	  $this->render('teste');
  }

  public function actionSeeder(){
  	do {
	  	header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		$time = date('r');
		echo "data: The server time is: {$time}\n\n";
		flush();
		ob_flush();
		sleep(1);
	} while(true);

  }

}
