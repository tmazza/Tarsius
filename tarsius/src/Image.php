<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

abstract class Image 
{
    const DEFAULT_RESOLUTION = 300;

    public $name;
    private $image;

    /**
     * Armazena nome da imagem e carrega arquivo de imagem para memória.
     *
     * @return Image 
     */
    abstract public function load(): Image;

    /**
     * Armazena nome do arquivo de imagem.
     *
     * @var string $name Caminho completo para a imagem a ser carregada
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * Extrai resolução do arquivo de imagem. Independente da biblioteca de 
     * imagem sendo utilizada. 
     * Caso não consiga inferir a resolução da imagem irá utilizar DEFAULT_RESOLUTION
     * 
     * @todo notificar quando resolução padrão é utilizada
     *
     * @link http://stackoverflow.com/a/12988682 referência
     */
    public function getResolucao(): int
    {
      $handle = fopen($this->name,'r');
      $string = fread($handle,20);
      fclose($handle);

      $data = bin2hex(substr($string,14,4));
      $x = hexdec(substr($data,0,4));
      $y = hexdec(substr($data,4,4));
      
      return $x == $y ? $x : self::DEFAULT_RESOLUTION;      
    }

}