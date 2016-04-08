<?php
namespace Bleez\Correios\Model;

use Bleez\Correios\Api\ConsultaInterface;
use Bleez\Correios\Api\Data\Dados;
use Bleez\Correios\Model\Endereco\Correios;
use Bleez\Correios\Model\Endereco\Republicavirtual;
use Bleez\Correios\Model\Endereco\Viacep;
use PhpSigep\Services\SoapClient\Real;

use Magento\Catalog\Model\Product;

/**
 * Class Consulta
 * @package Bleez\Correios\Model
 */
class Consulta extends \Magento\Framework\DataObject implements Dados, ConsultaInterface
{

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $_modelRegion;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $_totalsCollector;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var \Bleez\Correios\Helper\Sigep
     */
    protected $_helperSigep;

    /**
     * Consulta constructor.
     * @param \Magento\Directory\Model\Region $modelRegion
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\App\Request\Http $request
     * @param Product $product
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Bleez\Correios\Helper\Sigep $helperSigep
     */
    public function __construct(\Magento\Directory\Model\Region $modelRegion,
                                \Magento\Quote\Model\Quote $quote,
                                \Magento\Framework\App\Request\Http $request,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
                                \Magento\Framework\Pricing\Helper\Data $priceHelper,
                                \Bleez\Correios\Helper\Sigep $helperSigep
    )
    {
        $this->_quote = $quote;
        $this->_modelRegion = $modelRegion;
        $this->_request = $request;
        $this->_product = $product;
        $this->_totalsCollector = $totalsCollector;
        $this->_priceHelper = $priceHelper;
        $this->_helperSigep = $helperSigep;
    }

    /**
     * Consulta cep em diferentes serviços
     * @param int $cep
     * @return $this|bool
     */
    public function consultaCep($cep){

        $data = Correios::getEndereco($cep);
        if($data){
            $this->setData('logradouro', $data['logradouro']);
            $this->setData('bairro', $data['bairro']);
            $this->setData('cep', $data['cep']);
            $this->setData('cidade', $data['cidade']);
            $this->setData('uf', $this->getRegionId($data['uf']));
            return $this;
        }

        $this->_helperSigep->_initSigep();
        $phpSigep = new Real();
        $data  = $phpSigep->consultaCep($cep);

        if($data->getErrorCode() == null){
            $this->setData('logradouro', $data->getResult()->get('endereco'));
            $this->setData('bairro', $data->getResult()->get('bairro'));
            $this->setData('cep', $data->getResult()->get('cep'));
            $this->setData('cidade', $data->getResult()->get('cidade'));
            $this->setData('uf', $data->getResult()->get('uf'));
            return $this;
        }

        $data = Viacep::getEndereco($cep);
        if($data){
            $this->setData('logradouro', $data['logradouro']);
            $this->setData('bairro', $data['bairro']);
            $this->setData('cep', $data['cep']);
            $this->setData('cidade', $data['cidade']);
            $this->setData('uf', $this->getRegionId($data['uf']));
            return $this;
        }

        $data = Republicavirtual::getEndereco($cep);
        if($data){
            $this->setData('logradouro', $data['logradouro']);
            $this->setData('bairro', $data['bairro']);
            $this->setData('cep', $data['cep']);
            $this->setData('cidade', $data['cidade']);
            $this->setData('uf', $this->getRegionId($data['uf']));
            return $this;
        }

        return false;
    }

    /**
     * Estima Frete de um produto
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function estimarFrete(){
        $request = $this->_request->getParams();

        $params = new \Magento\Framework\DataObject();
        $params->setData($request);

        $this->_product->load($request['product']);

        $this->_quote->addProduct($this->_product, $params);

        $this->_quote->collectTotals();
        $shipping = $this->_quote->getShippingAddress();
        $shipping->setCountryId('BR');
        $shipping->setPostcode($request['cep']);
        $shipping->setCollectShippingRates(true);
        $this->_totalsCollector->collectAddressTotals($this->_quote, $shipping);
        $rates = $shipping->collectShippingRates()->getAllShippingRates();
        $data = array();
        foreach($rates as $k => $rate){
            $data[$k]['title'] = $rate->getMethodTitle();
            $data[$k]['price'] = $this->_priceHelper->currency($rate->getPrice(), true, false);
        }
        return json_encode($data);
    }


    /**
     * @param string $uf
     * @param string $country_id
     * @return mixed
     */
    public function getRegionId($uf, $country_id = "BR"){
        return $this->_modelRegion->loadByCode($uf, $country_id)->getId();
    }


    /**
     * @return string
     */
    public function getLogradouro()
    {
        return $this->getData('logradouro');
    }

    /**
     * @return string
     */
    public function getBairro()
    {
        return $this->getData('bairro');
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->getData('cep');
    }

    /**
     * @return string
     */
    public function getCidade()
    {
        return $this->getData('cidade');
    }

    /**
     * @return string
     */
    public function getUf()
    {
        return $this->getData('uf');
    }

}