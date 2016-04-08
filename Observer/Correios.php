<?php
namespace Bleez\Correios\Observer;

use Magento\Framework\Event\ObserverInterface;

class Correios implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Adiciona descrição na divisao de fretes;
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_scopeConfig->getValue('carriers/correios/divisao_frete')){
            $address = $observer->getShippingAssignment()->getShipping()->getAddress();
            $method = $observer->getShippingAssignment()->getShipping()->getMethod();
            $total = $observer->getTotal();

            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode() == $method) {
                    if($rate->getCarrier() == 'correios'){
                        $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle(). ' - ' . $rate->getMethodDescription();
                        $total->setShippingDescription(trim($shippingDescription, ' -'));
                        break;
                    }
                }
            }
        }
        return $observer;
    }
}
