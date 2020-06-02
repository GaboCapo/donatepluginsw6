import template from './gabcap-donate-detail.html.twig';

const { Component, Mixin } = Shopware;

Component.register('gabcap-donate-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            donate: null,
            isLoading: false,
            processSuccess: false,
            repository: null
        };
    },

    computed: {
        options() {
            return [
                { value: 'absolute', name: this.$t('gabcap-donate.detail.absoluteText') },
                { value: 'percentage', name: this.$t('gabcap-donate.detail.percentageText') }
            ];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('gabcap_donate');
        this.getDonate();
    },

    methods: {
        getDonate() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.donate = entity;
                });
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.donate, Shopware.Context.api)
                .then(() => {
                    this.getDonate();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('gabcap-donate.detail.errorTitle'),
                        message: exception
                    });
                });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
