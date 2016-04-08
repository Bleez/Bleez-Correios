<?php
/**
 * Created by PhpStorm.
 * User: thiago
 * Date: 11/03/16
 * Time: 16:15
 */

namespace Bleez\Correios\Model\Tracker;
use Magento\Config\Block\System\Config\Form\Field\Datetime;

/**
 * Class Request
 * Tracking
 * @package Bleez\Correios\Model\Tracker
 */
class Request
{

    /**
     * @param string $tracking
     * @return array
     */
    public function send($tracking)
    {
        $etiquetas = array();

        $etiqueta = new \PhpSigep\Model\Etiqueta();
        $etiqueta->setEtiquetaComDv(trim($tracking));
        $etiquetas[] = $etiqueta;

        $accessDataDeHomologacao = new \PhpSigep\Model\AccessDataHomologacao();
        $accessDataDeHomologacao->setUsuario('ECT');
        $accessDataDeHomologacao->setSenha('SRO');

        $params = new \PhpSigep\Model\RastrearObjeto();
        $params->setAccessData($accessDataDeHomologacao);
        $params->setEtiquetas($etiquetas);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->rastrearObjeto($params);
        $results = $result->getResult();
        foreach($results[0]->getEventos() as $k => $evento){
            $date = $this->_getDeliveryDateTime($evento->getDataHora());
            $_progresso[$k]['deliverydate'] = $date[0];
            $_progresso[$k]['deliverytime'] = $date[1];
            $_progresso[$k]['deliverylocation'] = $evento->getLocal().' '.$evento->getCidade().' '.$evento->getUf();
            $_progresso[$k]['activity'] = $evento->getDescricao()->__toString();
        }

        return $_progresso;
    }

    /**
     * @param Datetime $data
     * @return array
     */
    private function _getDeliveryDateTime($data)
    {

        $_deliveryDate = $data->format('Y-m-d');
        $_deliveryTime = $data->format('H:i');

        return array($_deliveryDate, $_deliveryTime);
    }
}