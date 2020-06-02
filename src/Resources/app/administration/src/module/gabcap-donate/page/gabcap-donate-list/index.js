import template from './gabcap-donate-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('gabcap-donate-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            donates: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('gabcap-donate.list.columnName'),
                routerLink: 'gabcap.donate.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'discount',
                dataIndex: 'discount',
                label: this.$t('gabcap-donate.list.columnDiscount'),
                inlineEdit: 'number',
                allowResize: true
            }, {
                property: 'discountType',
                dataIndex: 'discountType',
                label: this.$t('gabcap-donate.list.columnDiscountType'),
                allowResize: true
            }];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('gabcap_donate');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.donates = result;
            });
    }
});
