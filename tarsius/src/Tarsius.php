<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * @todo possibilitar alteração dos valores default ao iniciar
 */
class Tarsius
{
    /**
     * @var bool $debugEnable Se deve gerar dados intermediários para visualização
     *      e análise dos resultados parciais obtidos durante o processamento
     */
    static public $enableDebug = true;
    /**
     * @var int $minArea Área mínima para considerar objeto durante carregamento e busca
     *      das âncoras
     */
    static public $minArea = 800;
    /**
     * @var int $maxArea Área máxima para considerar objeto durante carregamento e busca
     *      das âncoras
     */
    static public $maxArea = 4000;
    /**
     * @var float $minMatchsObject Porcentagem mínima na comparação de dois objetos para 
     * considerá-los iguais.
     */ 
    static public $minMatchObject = 0.85;
    /**
     * @var float $maxExpansions Quantidade máxima de expansões na busca de um objeto. Região
     *      de busca é expandida enquanto nenhum objeto com $minMatchsObject mínimo seja 
     *      encontrado ou o limite $maxExpasions seja atingido.
     */ 
    static public $maxExpansions = 4;
    /**
     * @var float $expasionRate O quanto a região deve aumentar a cada expansão ($maxExpansions).
     *      Por exemplo, tendo uma área inicial de busca igual a 100 pixel e a taxa de expansão
     *      igual a 0.5, após a primeira iteração a área de busca será expandida para um quadrado
     *      de lado 150, após 225, etc. Aumentando a busca em 50% a cada iteração.
     */ 
    static public $expasionRate = 0.5;
    /**
     * @var int $searchArea Quantidade de 'escalas' para a definir o primeiro tamanho da 
     *      área de busca. Por exemplo, com uma resolução de 300dpi são aproximadamente
     *      11.81 pixel por milímetro, com $searchArea igual 10 a primeira área de busca
     *      seria um quadrado de 10*11.81 pixel de lado.
     */
    static public $searchArea = 10;
}