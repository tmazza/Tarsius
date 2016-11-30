<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * @todo Definir do utilizados no templeta
 */
class Mask
{
    private $name;

    /**
     * Armazena nome do arquivo de máscara em uso.
     *
     * @var string $name Caminho completo para a máscara a ser carregada
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Abre e interpreta arquivo JSON com as definições do template.
     */
    public function load(): Mask
    {
        $extension = pathinfo($this->name,PATHINFO_EXTENSION);
        if ($extension !== 'json') {
            throw new \Exception("Arquivo deve ser JSON.");
        }
        if (is_readable($this->name)) {
            $str = file_get_contents($templateFile);
        
            # TODO: cófigo de /src/Image
            // $data = json_decode($str,true);

            // if(isset($data['formatoSaida'])){
            //   $this->formatoSaida = json_decode($data['formatoSaida'],true);
            //   unset($data['formatoSaida']);
            // }

            // $this->medidas = $data;

            // $assinaturas = array();
            // for ($i = 1; $i < 5; $i++) {
            //     $image = Helper::load(__DIR__.'/ancoras/ancora' . $i . '.jpg');
            //     $assinaturas[$i] = $this->getAssinatura($image);
            // }
            // return $assinaturas;

            // $image = @imagecreatefromjpeg($this->name);
            if (!$image) {
                throw new \Exception("Erro desconhecido ao carregar arquivo '{$this->name}'.");
            }
        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura no arquivo '{$this->name}'.");
        } else {
            throw new \Exception("Arquivo '{$this->name}' não encontrado ou não existe.");
        }
        return $this;
    }
}