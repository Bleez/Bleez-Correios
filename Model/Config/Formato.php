<?php

namespace Bleez\Correios\Model\Config;

/**
 * Class Formato
 * @package Bleez\Correios\Model\Config
 */
class Formato implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => 'Envelope'),
            array('value' => 2, 'label' => 'Caixa'),
            array('value' => 3, 'label' => 'Cilindro'),
        );
    }
}