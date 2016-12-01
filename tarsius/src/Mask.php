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
    const FORMAT_OUTPUT = 'formatoSaida';
    const REGIONS = 'regioes';

    /**
     * @var static string $anchorsDir Caminho para diretório contendo as imagens das âncoras.
     */
    private static $anchorsDir = __DIR__ . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
    /**
     * @var string $name Caminho completo para a máscara a ser carregada
     */
    private $name;
    /**
     * @var string $type Tipo de manipulador de imagem que de ser usado. 
     *      Tipos possíveis definidos em ImageFactoty.
     */
    private $type;
    /**
     * @var string $formatOutput @todo documentar
     */
    private $formatOutput = false;
    /**
     * @var Image[] $anchors @todo documentar
     */
    private $anchors = [];    
    /**
     * @var mixed[] $regions @todo documentar
     */
    private $regions;

    /**
     * Armazena nome do arquivo de máscara em uso.
     *
     * @param string $name Caminho completo para a máscara a ser carregada
     * @param string $type Tipo de manipulador de imagem que de ser usado para carregamento
     *      das imagens das âncoras
     */
    public function __construct(string $name, string $type = ImageFactory::GD)
    {
        $this->name = $name;
        $this->type = $type;
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
            $str = file_get_contents($this->name);
            $data = json_decode($str,true);

            if (isset($data[self::FORMAT_OUTPUT])) {
                $this->formatOutput = json_decode($data[self::FORMAT_OUTPUT],true);
            }
            
            if (isset($data[self::REGIONS])) {
                $this->regions = $data[self::REGIONS];
            }

            /**
             * @todo permitir definição de tipo e quantidade de âncoras
             */
            for ($i = 1; $i < 5; $i++) {
                $imageName = self::$anchorsDir . "ancora{$i}.jpg"; 
                $this->anchors[$i] = ImageFactory::create($imageName, $this->type);
                $this->anchors[$i]->load();
            }

        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura no arquivo '{$this->name}'.");
        } else {
            throw new \Exception("Arquivo '{$this->name}' não encontrado ou não existe.");
        }
        return $this;
    }
}