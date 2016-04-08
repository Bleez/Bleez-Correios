<?php
namespace Bleez\Correios\Plugin\Shipping\Block\Adminhtml;

//TODO terminar

class View{

    protected $_urlBuilder;

    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }

    public function beforeGetEmailUrl(\Magento\Shipping\Block\Adminhtml\View $subject){
        $subject->addButton(
            'etiqueta',
            [
                'label' => __('Gerar Etiqueta'),
                'onclick' => 'setLocation(\'' . $this->gerarEtiqueta() . '\')'
            ]
        );
    }

    public function gerarEtiqueta(){
        return $this->_urlBuilder->getUrl('correios/etiqueta/gerar');
    }



}