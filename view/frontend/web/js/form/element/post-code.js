define([
    'underscore',
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'mask',
    'mage/url',
], function (_, ko, registry, Abstract, jquery, mask, url) {
    'use strict';

    var checkoutLoader = jquery('#checkout-loader');

    return Abstract.extend({
        defaults: {
            loading: ko.observable(false),
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        initialize: function () {
            this._super();
            jquery('#'+this.uid).mask('00000-000');
            return this;
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            if(options[value]){
                option = options[value];

                if (option['is_zipcode_optional']) {
                    this.error(false);
                    this.validation = _.omit(this.validation, 'required-entry');
                } else {
                    this.validation['required-entry'] = true;
                }

                this.required(!option['is_zipcode_optional']);

            }

            this.firstLoad = true;
        },


        onUpdate: function () {
            this.bubble('update', this.hasChanged());
            var validate = this.validate();
            //if(this.firstLoad){
            //    this.firstLoad = false;
            //    return;
            //}

            if(validate.valid == true && this.value() && this.value().length == 9){
                jquery('#checkout').append(checkoutLoader);

                var element = this;

                var value = this.value();
                value = value.replace('-', '');

                var ajaxurl = url.build("rest/V1/consultaCep/"+value);

                jquery.getJSON(ajaxurl, function(data) {
                    if(data.error){
                        alert(data.error);
                    }else{
                        if(registry.get(element.parentName + '.' + 'country_id')){
                            registry.get(element.parentName + '.' + 'country_id').value('BR');
                        }
                        if(registry.get(element.parentName + '.' + 'street.0')){
                            registry.get(element.parentName + '.' + 'street.0').value(data.logradouro);
                        }
                        if(registry.get(element.parentName + '.' + 'street.2')){
                            registry.get(element.parentName + '.' + 'street.2').value(data.bairro);
                        }
                        if(registry.get(element.parentName + '.' + 'city')){
                            registry.get(element.parentName + '.' + 'city').value(data.cidade);
                        }
                        if(registry.get(element.parentName + '.' + 'region_id')){
                            registry.get(element.parentName + '.' + 'region_id').value(data.uf);
                        }
                    }
                    jquery('#checkout-loader').remove();
                });
            }else{
                jquery('#checkout-loader').remove();
            }

        }
    });
});
