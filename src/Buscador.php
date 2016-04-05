<?php

class Buscador {

    public $corte = CORTE_PRETO;
    public $areaMinima = 200; // Valor base é alterado após localizacao da ancora 1
    public $areaMaxima = 5000; // Valor base é alterado após localizacao da ancora 1
    public $areaBuscaInicial = 100;
    public $qtdExpansoes = QTD_EXPANSOES_BUSCA;
    public $minMatch = MATCH_ANCORA;

    /**
     * Localiza $obj em $image. Com centro de busca em $ponto.
     * @param type $image
     * @param type $obj
     * @param type $ponto
     * TODO: algoritmo para expansao do quadrado caso objeto não seja encontrado com area inicial.
     */
    public function find($image, $objProcurado, $ponto) {
        $areaDeBusca = $this->areaBuscaInicial; // tamanho da aresta do quadrado de busca com centro em $ponto
        $qtdExpansoes = $this->qtdExpansoes;
        $match = false;
        do {
            $match = $this->analisaQuadrado($image, $objProcurado, $ponto, $areaDeBusca);
            # TODO: não refazer quadrados já análisados
            $areaDeBusca *= (1 + EXPANSAO_BUSCA);
            $qtdExpansoes--;
        } while (!$match && $qtdExpansoes > 0);

        return $match;
    }

    /**
     * Analisa região delimitada pelo quadrado com centro em $ponto e aretad e tamanho $areaDeBusca
     * @param type $image
     * @param type $objProcurado
     * @param type $ponto
     * @param type $areaDeBusca
     * @return boolean primeiro objeto dentro do quadrado semelhante a $objProcurado ou false caso não encontre
     */
    public function analisaQuadrado($image, $objProcurado, $ponto, $areaDeBusca) {
        // Define área do quadrado com centro em $ponto e aresta de tamana $areaDeBusca
        list($x0, $y0, $x1, $y1) = $this->getPontosQuadradoDeBusca($areaDeBusca, $ponto[0], $ponto[1]);
		    #Helper::rect($image, $x0, $y0, $x1, $y1, 'REGIAO_DE_BUSCA_' . microtime(true) . rand(0,999) . '_' . $areaDeBusca);
        // Separa pontos do quadrado
        $pontos = $this->getPontosDeQuadrado($image, $x0, $y0, $x1, $y1);
        #Helper::pintaPontos($image, $pontos, 'PONTOS__' . $areaDeBusca . '__' . microtime(true). rand(0,999), [255, 255, 0]);
        // Monta objetos
        $objetos = $this->separaObjetos($pontos, $this->areaMinima, $this->areaMaxima);
        #Helper::pintaObjetos($image, $objetos);

        // Define assinatura de cada objeto
        // Para cada assinatura comapra com $obj procurado
        foreach ($objetos as $objeto) {
            $igualdade = Assinatura::comparaFormas(Assinatura::get($objeto), $objProcurado);
            if ($igualdade > $this->minMatch) {
                #echo 'MATCH:  ' . $igualdade . '<br>';
                return $objeto;
            } else {
                #echo 'NOT MATCH:  ' . $igualdade . '<br>';
            }
        }
        return false;
    }

    /**
     * Retorna ponto superior esquerdo e inferior direito de quadrado.
     * Não permite que as coordenadas passem das bordas da imagem
     *
     * TODO: não permitir que x1 e y1 estejam fora da imagem.
     *
     * @param type $expansao
     * @param type $x0
     * @param type $y0
     * @return type
     */
    public function getPontosQuadradoDeBusca($expansao, $x0, $y0) {
        $x1 = $x0 + $expansao;
        $y1 = $y0 + $expansao;
        $x0 -= $expansao;
        $y0 -= $expansao;
        // Caso x0 esteja fora da imagem
        if ($x0 < 0) {
            $x1 += abs($x0); # se atingir o topo da imagem expande para baixo
            $x0 = 0;
        }
        // Caso y0 esteja fora da imagem
        if ($y0 < 0) {
            $y1 += abs($y0); # se atingir a borda esquerda da imagem expande para direita
            $y0 = 0;
        }
        return array($x0, $y0, $x1, $y1-1); # retorna um pixel a mais na altura vertical. tem q ver
    }

    /**
     * Recorda conjunto de pontos com RGB (cinza) abaixo de $corte
     * (considerado como ponto preto).
     * @param type $img
     * @param type $x0
     * @param type $y0
     * @param type $x1
     * @param type $y1
     * @return boolean - array linha coluna de ponto
     */
    public function getPontosDeQuadrado($img, $x0, $y0, $x1, $y1) {
        $pontos = array();
        $x0 = $x0 >= 0 ? $x0 : 0; // Não ultrapassa 0
        $y0 = $y0 >= 0 ? $y0 : 0; // Não ultrapassa 0

        for ($j = $y0; $j < $y1; $j++) {
            for ($i = $x0; $i < $x1; $i++) {
                list($r, $g, $b) = Helper::getRGB($img, $i, $j);
                $isBlack = (($r < $this->corte && $g < $this->corte && $b < $this->corte));
                if ($isBlack) {
                    $pontos[$i][$j] = true;
                }
            }
        }


        return $pontos;
    }

    /**
     * Identifica objetos em $pontos. Filtra por área mínima e máxima
     * @param type $pontos
     * @param type $min
     * @param type $max
     * @return type
     */
    public function separaObjetos($pontos, $min, $max) {
        $objetosConexos = new ConnectedComponent();
        $objetosConexos->setAreaMinima($min);
        $objetosConexos->setAreaMaxima($max);
        return $objetosConexos->getObjetos($pontos);
    }

    /**
     * Calcula o centro de massa/gravidade de $obj.
     * @param type $obj
     * @return type
     */
    private function getCentroDeMassa($obj) {
        if (!isset($obj['centro'])) {
            $area = count($obj['pontos']);
            $somaX = $somaY = 0;
            foreach ($obj['pontos'] as $p) {
                $somaX += $p[0];
                $somaY += $p[1];
            }
            return array(ceil($somaX / $area), ceil($somaY / $area));
        } else {
            return $obj['centro'];
        }
    }

    /**
     * Atializa valores de toletancia
     * @param type $areaBase
     */
    public function setTolerancia($areaBase) {
        $this->areaMinima = $areaBase * TOLERANCIA_MATCH;
        $this->areaMaxima = $areaBase * (1 + TOLERANCIA_MATCH);

        if ($this->areaMinima < 200) {
            $this->areaMinima = 200;
        }
        #echo $this->areaMinima .  ' - ' . $this->areaMaxima . '<br>';
    }

    public function setMinMatch($value){
      $this->minMath = $value;
    }

}
