<?php

namespace Bleez\Correios\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use PhpSigep\Model\Dimensao;
use PhpSigep\Model\CalcPrecoPrazo;
use PhpSigep\Services\SoapClient;
use PhpSigep\Model\AccessDataHomologacao;
use PhpSigep\Model\ServicoDePostagem;
use PhpSigep\Model\ServicoAdicional;
use PhpSigep\Services\SoapClient\Real;
use PhpSigep\Bootstrap;
use PhpSigep\Model\AccessData;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Correios
 * Shipping method Correios
 * @package Bleez\Correios\Model\Carrier
 */
class Correios extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'correios';

    /**
     * @var int
     */
    protected $_qtdFretes = 1;

    /**
     * @var \Bleez\Correios\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Bleez\Correios\Helper\Sigep
     */
    protected $_heperSigep;

    /**
     * Correios constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Xml\Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Shipping\Helper\Carrier $carrierHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Math\Division $mathDivision
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Bleez\Correios\Model\Tracker\Request $trackerRequest
     * @param \Bleez\Correios\Helper\Data $helper
     * @param \Bleez\Correios\Helper\Sigep $helperSigep
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Xml\Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Math\Division $mathDivision,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Bleez\Correios\Model\Tracker\Request $trackerRequest,
        \Bleez\Correios\Helper\Data $helper,
        \Bleez\Correios\Helper\Sigep $helperSigep,
        array $data = []
    ) {
        $this->readFactory = $readFactory;
        $this->_carrierHelper = $carrierHelper;
        $this->_coreDate = $coreDate;
        $this->_storeManager = $storeManager;
        $this->_configReader = $configReader;
        $this->string = $string;
        $this->mathDivision = $mathDivision;
        $this->_dateTime = $dateTime;
        $this->_httpClientFactory = $httpClientFactory;
        $this->trackerRequest = $trackerRequest;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_helper = $helper;
        $this->_helperSigep = $helperSigep;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['correios' => $this->getConfigData('name')];
    }


    /**
     * Retorna fretes para o magento
     * @param RateRequest $request
     * @return bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        try{
            //Inicia Lib
            $this->_initSigep();

            //Dimensões
            $dimensions = $this->_createDimensions($request);

            //Servicos
            $services = $this->_getServicos($request, $dimensions);

            $result = $this->_rateResultFactory->create();

            $allowed_services = explode(',', $this->getConfigData('types'));
            foreach($services->getResult() as $service){
                if($service->getErroCodigo() == 0 && in_array((string)$service->getServico()->getCodigo(), $allowed_services)) {
                    if($this->getConfigData('free_shipping_enabled') && $this->getConfigData('free_shipping_only') && $service->getServico()->getCodigo() != $this->getConfigData('free_shipping_service')){
                        continue;
                    }
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier('correios');
                    $method->setCarrierTitle($this->getConfigData('name'));
                    $method->setMethod($service->getServico()->getNome());
                    if($this->getConfigData('free_shipping_enabled') && $service->getServico()->getCodigo() == $this->getConfigData('free_shipping_service')){
                        $method->setMethodTitle($this->getConfigData('free_shipping_text') . sprintf($this->getConfigData('text_days'), $this->_calculateShippingDays($service)));
                        $method->setPrice(0);
                        $method->setCost(0);
                    }else{
                        $method->setMethodTitle($this->_getServiceName($service->getServico()->getCodigo()) . sprintf($this->getConfigData('text_days'), $this->_calculateShippingDays($service)));
                        $method->setPrice($this->_calculatePrice($service->getValor()));
                        $method->setCost($this->_calculatePrice($service->getValor()));
                    }
                    $method->setMethodDescription('Quantidade de fretes: '.$this->_qtdFretes);
                    $result->append($method);
                }
            }

            return $result;
        }catch(Exception $e){
            $this->_logger->error($e->getMessage());
            return false;
        }

    }

    /**
     * Inicia Lib Sigep
     */
    protected function _initSigep(){
        $this->_helperSigep->_initSigep();
    }

    /**
     * Seta e retorna dimensoes do pacote
     * @param RateRequest $request
     * @return \PhpSigep\Model\Dimensao
     */
    protected function _createDimensions(RateRequest $request){

        $dimensao = new Dimensao();
        $dimensao->setTipo($this->getConfigData('format'));

        $largura = 0;
        $altura = 0;
        $comprimento = 0;

        foreach($request->getAllItems() as $item){
            $largura += $item->getLargura();
            $altura += $item->getAltura();
            $comprimento += $item->getComprimento();
        }

        if($this->getConfigData('format') == 3){
            //Cilindro
            $dimensao->setDiametro($largura);
            $dimensao->setComprimento($comprimento);
        }else{
            //Caixa e envelope
            $dimensao->setAltura($altura);
            $dimensao->setComprimento($comprimento);
            $dimensao->setLargura($largura);
        }

        return $dimensao;
    }



    /**
     * Retorna servicos dos correios
     * @param RateRequest $request
     * @param \PhpSigep\Model\Dimensao $dimensions
     * @return \PhpSigep\Services\Result
     */

    protected function _getServicos(RateRequest $request, Dimensao $dimensions)
    {
        $params = new CalcPrecoPrazo();
        $params->setAccessData(new AccessDataHomologacao());
        $params->setCepOrigem($request->getPostcode());
        $params->setCepDestino($request->getDestPostcode());
        $params->setServicosPostagem(ServicoDePostagem::getAll());
        $params->setAjustarDimensaoMinima(true);
        $params->setDimensao($dimensions);

        $servicosAdicionais = array();

        if ($this->getConfigData('aviso_recebimento')) {
            $avisoDeRecebimento = new ServicoAdicional();
            $avisoDeRecebimento->setCodigoServicoAdicional(ServicoAdicional::SERVICE_AVISO_DE_RECEBIMENTO);
            $servicosAdicionais[] = $avisoDeRecebimento;
        }

        if ($this->getConfigData('mao_propria')) {
            $maoPropria = new ServicoAdicional();
            $maoPropria->setCodigoServicoAdicional(ServicoAdicional::SERVICE_MAO_PROPRIA);
            $servicosAdicionais[] = $maoPropria;
        }

        if ($this->getConfigData('valor_declarado') && $request['package_value']) {
            $valorDeclarado = new ServicoAdicional();
            $valorDeclarado->setCodigoServicoAdicional(ServicoAdicional::SERVICE_VALOR_DECLARADO);
            $valorDeclarado->setValorDeclarado($request['package_value']);
            $servicosAdicionais[] = $valorDeclarado;
        }

        if($this->getConfigData('ect') && $this->getConfigData('password')){

            $accessData = new AccessData();
            $accessData->setUsuario($this->getConfigData('ect'));
            $accessData->setSenha($this->getConfigData('password'));
            $params->setAccessData($accessData);

        }

        $params->setServicosAdicionais($servicosAdicionais);
        $params->setPeso($this->_calculateWeightShipping($request));

        $phpSigep = new Real();
        return $phpSigep->calcPrecoPrazo($params);
    }

    /**
     * Soma prazo de entrega do servico e coloca adicionais
     * @param $service
     * @return int
     */
    protected function _calculateShippingDays($service){
        if($this->getConfigData('servicesnames')){
            return (int)$service->getPrazoEntrega()+(int)$this->getConfigData('add_days');
        }
        return '';
    }

    /**
     * Divide o frete se passar do limite de peso
     * @param RateRequest $request
     * @return float|string
     */
    protected function _calculateWeightShipping(RateRequest $request){
        if($this->getConfigData('divisao_frete')) {
            if ($request->getPackageWeight() > $this->_helper->getLimitWeight()) {
                $shipWeight = 0;
                for ($k = 1; $k < $request->getPackageQty(); $k++) {
                    if ($request->getPackageWeight() / $k <= $this->_helper->getLimitWeight()) {
                        $shipWeight = $request->getPackageWeight() / $k;
                        $this->_qtdFretes = $k;
                        break;
                    }
                }
                if ($shipWeight > 0) {
                    return number_format($shipWeight, 2);
                }
            }
        }

        return $request->getPackageWeight();
    }

    /**
     * @param float $valor
     * @return float
     */
    protected function _calculatePrice($valor){
        return ($valor*$this->_qtdFretes)+(float)$this->getConfigData('add_tax');
    }

    protected function _getServiceName($codigo){
        $names = $this->getConfigData('servicesnames');
        foreach(json_decode($names) as $name){
            if($name->id == $codigo){
                return $name->name;
            }
        }
    }

    /* Tracking */

    /**
     * @return bool
     */

    public function isTrackingAvailable(){
        return true;
    }

    /**
     * @param $trackings
     * @return array
     */
    public function getTracking($trackings)
    {
        return array('1', '2');
    }

    /**
     * @param string $number
     * @return \Bleez\Correios\Model\Tracker\Request
     */
    public function getTrackingInfo($number){

        $data = $this->trackerRequest->send($number);

        $tracking = $this->_trackStatusFactory->create();
        $tracking->setCarrier($this->_code);
        $tracking->setCarrierTitle($this->getConfigData('name'));
        $tracking->setTracking($number);
        $tracking->setProgressdetail($data);

        return $tracking;
    }

    /**
     * @param \Magento\Framework\DataObject $request
     * @return null;
     */

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $this->setRequest($request);
    }

}