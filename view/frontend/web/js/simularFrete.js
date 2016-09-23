define([
    'jquery',
    'underscore',
    'mask',
    'Magento_Checkout/js/checkout-data',
    'mage/url',
    'uiComponent',
], function ($, _, mask, storage, url, Component) {
    'use strict';

    $('#cep').mask('99999-999')

    if(storage.getShippingAddressFromData()) {
        $('#cep').val(storage.getShippingAddressFromData().postcode);
    }

    $('#simularFrete').on('submit', function(e){
        e.preventDefault();

        var data = $('#product_addtocart_form').serializeArray();
        var info = {};
        for (var i = 0, l = data.length; i < l; i++) {
            info[data[i].name] = data[i].value;
        }
        info['cep'] = $('#cep').val();

        $('#containerFrete').slideUp();

        $.ajax({
            url: $('#simularFrete').attr('action'),
            method: 'get',
            data: info,
            beforeSend: function(){
                $('#sendFrete').val('Aguarde...');
            },
            success: function(data){
                $('#containerFrete').html('');
                data = $.parseJSON(data);
                _.each(data, function(element, index, list){
                    $('#containerFrete').append('<li>'+element.title+' - '+element.price+'</li>');
                })
                $('#containerFrete').slideDown('slow');
                $('#sendFrete').val('Calcular Frete');
                if(storage.getShippingAddressFromData()){
                    var storageData = storage.getShippingAddressFromData();
                    storageData.postcode = $('#cep').val();
                    storage.setShippingAddressFromData(storageData);
                }else{
                    storage.setShippingAddressFromData({postcode: $('#cep').val(), country_id: '', region: '', region_id: ''});
                }
            },
            error: function(){
                alert("Não foi possivel simular o frete");
            }
        });
    });
});