<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

/**
 * Visualização dos resultados obtidos durante processamento
 */
class DistribuidoController extends BaseController
{

    /**
     * Mostrar imagem com região e valores do processamento
     */
    public function actionVer($id, $renovar=false)
    {
        Yii::app()->clientScript->registerScriptFile($this->wb.'/jquery.elevatezoom.min.js');
        $model = Distribuido::model()->findByPk((int)$id);

        try {
            if(is_null($model)){
                throw new Exception("Registro finalizado ID:'$id' não encontrado.", 3);
            } else {

                $debugImage = self::getDebugImage($model,$renovar);

                $this->render('ver',[
                    'model'=>$model,
                    'debugImage'=>$debugImage,
                ]);
            }
        } catch(Exception $e){

            HView::fMsg(CHtml::tag('pre', [], $e->getMessage()));                

            $this->render('verComErro',[
                'model'=>$model,
            ]);
        }

    }

    /**
     * Gera imagem de debug
     */
    public static function getDebugImage($dist, $renovar=false)
    {
        $baseDir = Yii::app()->params['runtimeDir'] . "/trab-{$dist->trabalho->id}/";
        $imgDir = $baseDir . 'img/';
        $file = $dist->nome;

        # cria diretorio para imagens de debug
        if (!is_dir($imgDir)) {
            CFileHelper::createDirectory($imgDir, 0777);
        }

        $reviewImage = $imgDir . substr($file,0,-4) . '.png';


        if(!file_exists($reviewImage) || $renovar){
            
            $output = json_decode($dist->resultado->conteudo, true);

            # Caso seja somente uma mensagem de erro
            if(is_string($output)){
                throw new Exception($output);
            }

            # carrega imagem original
            $sourceDir = $dist->trabalho->sourceDir;
            if (substr($sourceDir, -1) != '/') {
                $sourceDir .= '/';
            }
            $originalFile = $sourceDir . '/' . $dist->nome;

            if (!is_readable($originalFile)) {
                throw new Exception("Sem permissão de leitura em '{originalFile}'.");
            } else if(!file_exists($originalFile)){
                throw new Exception("Arquivo '{originalFile}' não encontrado.");
            }
            $original = imagecreatefromjpeg($originalFile);

            $pathTemplate = Yii::app()->params['templatesDir'] . '/' . $dist->trabalho->template . '/template.json';

            $strTempalte = file_get_contents($pathTemplate);
            $template = json_decode($strTempalte,true);

            $anchors = isset($output['anchors']) ? $output['anchors'] : [];
            if(count($anchors) > 0) {
                imagerectangle ($original, $anchors[1][0], $anchors[1][1] , $anchors[3][0] , $anchors[3][1] , imagecolorallocate($original, 0, 255, 0) );
                imagerectangle ($original, $anchors[2][0], $anchors[2][1] , $anchors[4][0] , $anchors[4][1] , imagecolorallocate($original, 255, 0, 0) );
            }

            $escala = $output['scale'] / 25.4;
            $regioes = $output['regionsResult'];    
            # Desenha formas nas posições avaliadas
            foreach ($regioes as $r) {
                if(!is_array($r[1])){ # skip OCR
                    $w = $escala * $template[Tarsius\Mask::ELLIPSE_WIDTH] ;
                    $h = $escala * $template[Tarsius\Mask::ELLIPSE_HEIGHT];
                    $x = $r[2];
                    $y = $r[3];

                    $minMatchEllipse = isset($r[4]) ? $r[4] : Tarsius\Tarsius::$minMatchEllipse;          

                    if($r[1] > $minMatchEllipse) {
                      imagefilledellipse($original,$x,$y,$w,$h, imagecolorallocatealpha($original,255,255,0,75));
                    } else {
                      imageellipse($original,$x,$y,$w,$h, imagecolorallocate($original, 255,0,255));
                    }
                }
            }

            imagepng($original,$reviewImage);
        }
        return  Yii::app()->baseUrl . '/../data/runtime/trab-'.$dist->trabalho->id.'/img/'.substr($file,0,-4) . '.png';
    }
}