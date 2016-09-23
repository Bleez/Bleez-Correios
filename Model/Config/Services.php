<?php

namespace Bleez\Correios\Model\Config;

use PhpSigep\Model\ServicoDePostagem;

/**
 * Class Services
 * @package Bleez\Correios\Model\Config
 */
class Services implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $sufix = array(
        41106 => 'Pac sem contrato',
        41068 => 'Pac com contrato',
        40096 => 'Sedex sem contrato',
        40436 => 'Sedex sem contrato',
        40444 => 'Sedex sem contrato',
        81019 => 'E-Sedex com contrato',
        40045 => 'E-Sedex com contrato',
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $servicos = ServicoDePostagem::getAll();
        $retorno = array();
        foreach($servicos as $servico){
            if(array_key_exists($servico->getCodigo(), $this->sufix)){
                $nome = $servico->getNome().' - '.$this->sufix[$servico->getCodigo()];
            }else{
                $nome = $servico->getNome();
            }
            $retorno[] = array('value' => $servico->getCodigo(), 'label' => $nome);
        }

        return $retorno;
    }
}