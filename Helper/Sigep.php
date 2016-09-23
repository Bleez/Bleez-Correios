<?php

namespace Bleez\Correios\Helper;

use PhpSigep\Model\AccessDataHomologacao;
use PhpSigep\Bootstrap;

class Sigep {

    /**
     * Inicia Sigep
     */
    public function _initSigep(){
        $accessDataParaAmbienteDeHomologacao = new AccessDataHomologacao();
        $config = new \PhpSigep\Config();
        $config->setAccessData($accessDataParaAmbienteDeHomologacao);
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
    }
}