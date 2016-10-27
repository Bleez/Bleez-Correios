<?php

namespace Bleez\Correios\Block\Catalog\Product\View;

class CalcularFrete extends \Magento\Catalog\Block\Product\View\AbstractView {

    public function canShow(){
        return $this->getProduct()->isSalable();
    }


}