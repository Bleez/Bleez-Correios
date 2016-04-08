# Bleez Correios

Modulo de correios para magento 2

## Como instalar

### Via Composer

```sh
composer require bleez/correios
php -f bin/magento module:enable --clear-static-content Bleez_Correios
php -f bin/magento setup:upgrade
```

## Features

* Calculo de frete por peso, largura, altura e comprimento
* Diferentes formatos de embalafem
* Alerta de limite de peso, largura, altura e comprimento no cadastro de produto
* Divisão de frete (Ex: caso o pacote ultrapasse o limite de peso dos correios é contabilizado mais de um frete)
* Rastreamento de objetos
* Calculo de frete na pagina do produto
* Autopreenchimento dos formularios do carrinho e checkout baseado no CEP
