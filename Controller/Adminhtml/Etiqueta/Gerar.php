<?php
namespace Bleez\Correios\Controller\Adminhtml\Etiqueta;

use PhpSigep\Model\AccessDataHomologacao;
use PhpSigep\Model\AccessData;
use PhpSigep\Model\SolicitaEtiquetas;
use PhpSigep\Services\SoapClient\Real;
use PhpSigep\Bootstrap;

//Todo Terminar

class Gerar extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $usuario = '';
        $senha = '';
        $cnpjEmpresa = '';

        $accessData = new AccessDataHomologacao();
        $accessData->setUsuario($usuario);
        $accessData->setSenha($senha);
        $accessData->setCnpjEmpresa($cnpjEmpresa);

        $config = new \PhpSigep\Config();
        $config->setAccessData($accessData);
        $config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
        $config->setCacheOptions(
            array(
                'storageOptions' => array(
                    'enabled' => false,
                    'ttl' => 10,
                    'cacheDir' => sys_get_temp_dir(),
                ),
            )
        );

        Bootstrap::start($config);

        $params = new SolicitaEtiquetas();
        $params->setQtdEtiquetas(1);
        $params->setServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_41068);
        $params->setAccessData($accessData);

        $phpSigep = new Real();
        var_dump($phpSigep->solicitaEtiquetas($params));die;
    }

}
