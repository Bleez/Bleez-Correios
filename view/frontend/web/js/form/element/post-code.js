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

    return Abstract.extend({
        defaults: {
            loading: ko.observable(false),
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
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

            jquery('#'+this.uid).mask('99999-999');

            this.firstLoad = true;
        },


        onUpdate: function () {
            this.bubble('update', this.hasChanged());
            var validate = this.validate();
            //if(this.firstLoad){
            //    this.firstLoad = false;
            //    return;
            //}

            if(validate.valid == true && this.value().length == 9){
                this.loading(true);

                var element = this;

                var value = this.value();
                value = value.replace('-', '');

                var ajaxurl = url.build("rest/V1/consultaCep/"+value);

                jquery.getJSON(ajaxurl, function(data) {
                    if(registry.get(element.parentName + '.' + 'country_id')){
                        registry.get(element.parentName + '.' + 'country_id').value('BR');
                    }
                    if(registry.get(element.parentName + '.' + 'street.0')){
                        registry.get(element.parentName + '.' + 'street.0').value(data.logradouro);
                    }
                    if(registry.get(element.parentName + '.' + 'city')){
                        registry.get(element.parentName + '.' + 'city').value(data.cidade);
                    }
                    if(registry.get(element.parentName + '.' + 'region_id')){
                        registry.get(element.parentName + '.' + 'region_id').value(data.uf);
                    }
                    element.loading(false);
                });
            }else{
                this.loading(false);
            }

        }
    });
});
