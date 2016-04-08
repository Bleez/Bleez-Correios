<?php

namespace Bleez\Correios\Block\Adminhtml\Catalog\Product\Edit;

class CorreiosLimit extends \Magento\Backend\Block\Template
{
    /**
     * @var \Bleez\Correios\Helper\Data
     */
    protected $_helper;

    /**
     * CorreiosLimit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bleez\Correios\Helper\Data $helper
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Bleez\Correios\Helper\Data $helper, array $data)
    {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * See \Bleez\Correios\Helper\Data
     * @return int
     */
    public function getLimitWeight(){
        return $this->_helper->getLimitWeight();
    }

    /**
     * See \Bleez\Correios\Helper\Data
     * @return string
     */
    public function getLimitSizes(){
        return $this->_helper->getLimitSizes();
    }

    /**
     * See \Bleez\Correios\Helper\Data
     * @return string
     */
    public function getFormat(){
        return $this->_helper->getFormat();
    }
}