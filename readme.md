# Bleez Correios

Modulo de correios para magento 2

## Como instalar

### Via Composer

```sh
composer require bleez/correios
php bin/magento module:enable --clear-static-content Bleez_Correios
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy //ou php bin/magento setup:static-content:deploy pt_BR
```

### Configurar cep de origem

* Configuration -> Sales -> Shipping Settings -> Origin -> Zip Code

## Features

* Calculo de frete por peso, largura, altura e comprimento
* Diferentes formatos de embalafem
* Alerta de limite de peso, largura, altura e comprimento no cadastro de produto
* Divisão de frete (Ex: caso o pacote ultrapasse o limite de peso dos correios é contabilizado mais de um frete)
* Rastreamento de objetos
* Calculo de frete na pagina do produto
* Autopreenchimento dos formularios do carrinho e checkout baseado no CEP
