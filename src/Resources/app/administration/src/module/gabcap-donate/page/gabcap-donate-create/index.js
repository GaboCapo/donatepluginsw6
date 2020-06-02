const { Component } = Shopware;

Component.extend('gabcap-donate-create', 'gabcap-donate-detail', {
    methods: {
        getDonate() {
            this.donate = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.donate, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'gabcap.donate.detail', params: { id: this.donate.id } });
                }).catch((exception) => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: this.$t('gabcap-donate.detail.errorTitle'),
                        message: exception
                    });
                });
        }
    }
});
