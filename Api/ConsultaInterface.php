<?php

namespace Bleez\Correios\Api;

interface ConsultaInterface
{
    /**
     * Consulta Cep
     *
     * @param int $cep
     * @return \Bleez\Correios\Api\Data\Dados|Bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function consultaCep($cep);

    /**
     * Estima frete do produto
     * @return string
     */
    public function estimarFrete();
}
