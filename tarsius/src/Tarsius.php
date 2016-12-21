<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * Configuração dos parâmetros usados para processamento
 */
class Tarsius
{
    /**
     * @var bool $debugEnable Se deve gerar dados intermediários para visualização
     *      e análise dos resultados parciais obtidos durante o processamento
     */
    static public $enableDebug = false;
    /**
     * @var string $debugDir Diretório onde serão salvos os arquivos gerados durante debug
     *      pelo processo.
     */
    static public $debugDir = __DIR__ . DIRECTORY_SEPARATOR . '..' 
                                      . DIRECTORY_SEPARATOR . '..' 
                                      . DIRECTORY_SEPARATOR . 'debug';
    /**
     * @var string $runtimeDir Diretório para manipulação de arquivos gerados e acessados
     *      pelo processo.
     */
    static public $runtimeDir = __DIR__ . DIRECTORY_SEPARATOR . '..' 
                                        . DIRECTORY_SEPARATOR . '..' 
                                        . DIRECTORY_SEPARATOR . 'runtime';
    /**
     * @var int $threshold Corte entre pixel pretos e brancos. 
     * 
     * @todo usar limiar dinâmico
     * @todo possibilitar configuração em tempo de execução
     */ 
    static public $threshold = 128;
    /**
     * @var int $minArea Área mínima para considerar objeto durante carregamento e busca
     *      das âncoras. Após encontrar a primeira âncora o valor da área desta será usado
     *      como referência de área máxima tendo uma tolerância de $areaTolerance
     */
    static public $minArea = 400;
    /**
     * @var int $maxArea Área máxima para considerar objeto durante carregamento e busca
     *      das âncoras. Após encontrar a primeira âncora o valor da área desta será usado
     *      como referência de área máxima tendo uma tolerância de $areaTolerance
     */
    static public $maxArea = 4000;
    /**
     * @var float $areaTolerance Tolerância na busca das âncoras, usado após encontrar a primeira
     *      âncora. Por exemplo, caso a área da âncora encontrada seja de 1000 pixel e $areaTolerance
     *      seja 0.4 o valor de $minArea e $maxArea serão, respectivamente, 600 e 1400.
     */
    static public $areaTolerance = 0.4;
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
    /**
     * @var float $minMatchEllipse Valor mínimo para considerar uma elipse preenchida.
     */
    static public $minMatchEllipse = 0.3;
    /**
     * @var int $templateValidationTolerance Quantidade de diferenças aceitas durante 
     *      comparação de validador de template. Mask.validateMask
     */
    static public $templateValidationTolerance = 3;
    /**
     * @var boll $dynamicPointReference Para definir um ponto, por exemplo o centro de um elipse, as
     * quatro âncoras são utilizadas como referência. Caso $dynamicPointReference não seja aplicado
     * todos os pontos terão o mesmo 'peso' na hora definir o ponto real (único). Com o uso de $dynamicPointReference
     * quanto mais próximo da âncora de referência o ponto estiver maior será o seu 'peso' ao definir
     * o ponto real.
     */
    static public $dynamicPointReference = false;

    /**
     * Altera valores default dos parâmetros
     */
    public static function config($config)
    {
        $parameters = self::getParameters();
        foreach ($parameters as $param) {
            self::${$param} = isset($config[$param]) ? $config[$param] : self::${$param};
        }
    }

    /**
     * Retorna a lista de parâmetros públicos da classe
     *
     * @return array
     */
    public static function getParameters()
    {
        $class = new \ReflectionClass(__CLASS__);
        return array_keys($class->getStaticProperties());
    }
}