<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

/**
 * Geração e manipulação de templates.
 */
class TemplateCommand extends CConsoleCommand {

    private $resolucaoBase = 300;
    
    /**
     * Reprocessa template $nome, usando como diretório base 
     * Yii::app()->params['templatesDir'].
     *
     * @param string $nome
     */
    public function actionIndex($nome=false)
    {
        if($nome){
            $templateDir = Yii::app()->params['templatesDir'] . '/' . $nome;
            if (is_dir($templateDir)) {
                $config = require $templateDir . '/gerador.php';
                $gerador = new Tarsius\MaskGenerator($nome, $templateDir . '/base.jpg', $config);
                try {
                    $gerador->generate();
                    echo "Template '{$nome}' gerado. Preview disponível em {$templateDir}/template.png.\n";
                } catch (Exception $e) {
                    echo $e->getMessage() . "\n" . $e;              
                }
            } else {
                echo "Diretório '{$dir}' não encontrado ou não existe.";
            }
        } else {
            echo "\tQual o nome do template?\n".
                 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
        }
    }

}