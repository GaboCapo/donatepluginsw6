(this.webpackJsonp = this.webpackJsonp || []).push([["donate-example"], {
    "+k/5": function (e, t) {
        e.exports = '{% block gabcap_donate_list %}\n    <sw-page class="gabcap-donate-list">\n        {% block gabcap_donate_list_smart_bar_actions %}\n            <template slot="smart-bar-actions">\n                <sw-button variant="primary" :routerLink="{ name: \'gabcap.donate.create\' }">\n                    {{ $t(\'gabcap-donate.list.addButtonText\') }}\n                </sw-button>\n            </template>\n        {% endblock %}\n\n        <template slot="content">\n            {% block gabcap_donate_list_content %}\n                <sw-entity-listing\n                    v-if="donates"\n                    :items="donates"\n                    :repository="repository"\n                    :showSelection="false"\n                    :columns="columns"\n                    detailRoute="gabcap.donate.detail">\n                </sw-entity-listing>\n            {% endblock %}\n        </template>\n    </sw-page>\n{% endblock %}\n'
    }, "+pqe": function (e, t) {
        e.exports = '{% block sw_product_detail_attribute_sets %}\n    {% parent() %}\n    <sw-card :title="$t(\'sw-product.detail.donateCardLabel\')"\n             :isLoading="isLoading">\n        <sw-inherit-wrapper v-if="!isLoading"\n                            v-model="product.extensions.donates"\n                            :inheritedValue="parentProduct.extensions ? parentProduct.extensions.donates : null"\n                            :hasParent="!!parentProduct.id"\n                            :label="$t(\'sw-product.detail.donateSelectLabel\')"\n                            isAssociation\n                            @inheritance-remove="saveProduct"\n                            @inheritance-restore="saveProduct">\n            <template #content="{ currentValue, isInherited, updateCurrentValue }">\n                <sw-entity-many-to-many-select\n                    :localMode="product.isNew()"\n                    :entityCollection="currentValue"\n                    @input="updateCurrentValue"\n                    labelProperty="name"\n                    :disabled="isInherited"\n                    :key="isInherited"\n                    :placeholder="$t(\'sw-product.detail.donateSelectPlaceholder\')">\n                </sw-entity-many-to-many-select>\n            </template>\n        </sw-inherit-wrapper>\n    </sw-card>\n{% endblock %}\n'
    }, "7B7p": function (e, t) {
        const {Component: n} = Shopware;
        n.extend("gabcap-donate-create", "gabcap-donate-detail", {
            methods: {
                getDonate() {
                    this.donate = this.repository.create(Shopware.Context.api)
                }, onClickSave() {
                    this.isLoading = !0, this.repository.save(this.donate, Shopware.Context.api).then(() => {
                        this.isLoading = !1, this.$router.push({
                            name: "gabcap.donate.detail",
                            params: {id: this.donate.id}
                        })
                    }).catch(e => {
                        this.isLoading = !1, this.createNotificationError({
                            title: this.$t("gabcap-donate.detail.errorTitle"),
                            message: e
                        })
                    })
                }
            }
        })
    }, AyTr: function (e, t, n) {
        "use strict";
        n.r(t);
        var a = n("+k/5"), s = n.n(a);
        const {Component: i} = Shopware, {Criteria: o} = Shopware.Data;
        i.register("gabcap-donate-list", {
            template: s.a,
            inject: ["repositoryFactory"],
            data: () => ({repository: null, donates: null}),
            metaInfo() {
                return {title: this.$createTitle()}
            },
            computed: {
                columns() {
                    return [{
                        property: "name",
                        dataIndex: "name",
                        label: this.$t("gabcap-donate.list.columnName"),
                        routerLink: "gabcap.donate.detail",
                        inlineEdit: "string",
                        allowResize: !0,
                        primary: !0
                    }, {
                        property: "discount",
                        dataIndex: "discount",
                        label: this.$t("gabcap-donate.list.columnDiscount"),
                        inlineEdit: "number",
                        allowResize: !0
                    }, {
                        property: "discountType",
                        dataIndex: "discountType",
                        label: this.$t("gabcap-donate.list.columnDiscountType"),
                        allowResize: !0
                    }]
                }
            },
            created() {
                this.repository = this.repositoryFactory.create("gabcap_donate"), this.repository.search(new o, Shopware.Context.api).then(e => {
                    this.donates = e
                })
            }
        });
        var l = n("OMZE"), r = n.n(l);
        const {Component: d, Mixin: u} = Shopware;
        d.register("gabcap-donate-detail", {
            template: r.a,
            inject: ["repositoryFactory"],
            mixins: [u.getByName("notification")],
            metaInfo() {
                return {title: this.$createTitle()}
            },
            data: () => ({donate: null, isLoading: !1, processSuccess: !1, repository: null}),
            computed: {
                options() {
                    return [{
                        value: "absolute",
                        name: this.$t("gabcap-donate.detail.absoluteText")
                    }, {value: "percentage", name: this.$t("gabcap-donate.detail.percentageText")}]
                }
            },
            created() {
                this.repository = this.repositoryFactory.create("gabcap_donate"), this.getDonate()
            },
            methods: {
                getDonate() {
                    this.repository.get(this.$route.params.id, Shopware.Context.api).then(e => {
                        this.donate = e
                    })
                }, onClickSave() {
                    this.isLoading = !0, this.repository.save(this.donate, Shopware.Context.api).then(() => {
                        this.getDonate(), this.isLoading = !1, this.processSuccess = !0
                    }).catch(e => {
                        this.isLoading = !1, this.createNotificationError({
                            title: this.$t("gabcap-donate.detail.errorTitle"),
                            message: e
                        })
                    })
                }, saveFinish() {
                    this.processSuccess = !1
                }
            }
        });
        n("7B7p");
        var c = n("R/kZ"), p = n("R2cx");
        const {Module: b} = Shopware;
        b.register("gabcap-donate", {
            type: "plugin",
            name: "Donate",
            title: "gabcap-donate.general.mainMenuItemGeneral",
            description: "sw-property.general.descriptionTextModule",
            color: "#ff3d58",
            icon: "default-basic-shape-heart",
            snippets: {"de-DE": c, "en-GB": p},
            routes: {
                list: {component: "gabcap-donate-list", path: "list"},
                detail: {
                    component: "gabcap-donate-detail",
                    path: "detail/:id",
                    meta: {parentPath: "gabcap.donate.list"}
                },
                create: {component: "gabcap-donate-create", path: "create", meta: {parentPath: "gabcap.donate.list"}}
            },
            navigation: [{
                label: "gabcap-donate.general.mainMenuItemGeneral",
                color: "#ff3d58",
                path: "gabcap.donate.list",
                icon: "default-basic-shape-heart",
                position: 100
            }]
        });
        var w = n("+pqe"), m = n.n(w);
        const {Component: h} = Shopware;
        h.override("sw-product-detail-base", {
            template: m.a, computed: {
                productRepository() {
                    return this.repositoryFactory.create("product")
                }
            }, methods: {
                saveProduct() {
                    this.product && this.productRepository.save(this.product, Shopware.Context.api)
                }
            }
        });
        n("oRtI");
        var g = n("XM2Y"), y = n.n(g);
        const {Component: x} = Shopware;
        x.extend("gabcap-cart-contains-donate", "sw-condition-base", {template: y.a});
        const {Application: v} = Shopware;
        v.addServiceProviderDecorator("ruleConditionDataProviderService", e => (e.addCondition("gabcapDonateContainsDonate", {
            component: "gabcap-cart-contains-donate",
            label: "sw-condition.condition.cartContainsDonate.label",
            scopes: ["cart"]
        }), e))
    }, OMZE: function (e, t) {
        e.exports = '{% block gabcap_donate_detail %}\n    <sw-page class="gabcap-donate-detail">\n        <template slot="smart-bar-actions">\n            <sw-button :routerLink="{ name: \'gabcap.donate.list\' }">\n                {{ $t(\'gabcap-donate.detail.cancelButtonText\') }}\n            </sw-button>\n\n            <sw-button-process\n                :isLoading="isLoading"\n                :processSuccess="processSuccess"\n                variant="primary"\n                @process-finish="saveFinish"\n                @click="onClickSave">\n                {{ $t(\'gabcap-donate.detail.saveButtonText\') }}\n            </sw-button-process>\n        </template>\n\n        <template slot="content">\n            <sw-card-view>\n                <sw-card v-if="donate" :isLoading="isLoading">\n                    <sw-field :label="$t(\'gabcap-donate.detail.nameLabel\')" v-model="donate.name"></sw-field>\n                    <sw-field :label="$t(\'gabcap-donate.detail.discountLabel\')" v-model="donate.discount" type="number"></sw-field>\n\n                    <sw-field type="radio"\n                              :label="$t(\'gabcap-donate.detail.discountTypeLabel\')"\n                              v-model="donate.discountType"\n                              :options="options">\n                    </sw-field>\n\n                    <sw-entity-many-to-many-select\n                        :localMode="donate.isNew()"\n                        :label="$t(\'gabcap-donate.detail.assignProductsLabel\')"\n                        v-model="donate.products">\n                    </sw-entity-many-to-many-select>\n                </sw-card>\n            </sw-card-view>\n        </template>\n    </sw-page>\n{% endblock %}\n'
    }, "R/kZ": function (e) {
        e.exports = JSON.parse('{"gabcap-donate":{"general":{"mainMenuItemGeneral":"Donate","descriptionTextModule":"Verwalte die Donates hier"},"list":{"addButtonText":"Donate hinzufügen","columnName":"Name","columnDiscountType":"Rabatt Typ","columnDiscount":"Rabatt"},"detail":{"nameLabel":"Name","discountLabel":"Rabatt","discountTypeLabel":"Rabatt Typ","assignProductsLabel":"Produkte zuweisen","cancelButtonText":"Abbrechen","saveButtonText":"Speichern","errorTitle":"Fehler beim Speichern des Donates","absoluteText":"Absolut","percentageText":"Prozentual"}},"sw-product":{"detail":{"donateCardLabel":"Donates","donateSelectLabel":"Zugewiesene Donates","donateSelectPlaceholder":"Donate hinzufügen..."}},"sw-condition":{"condition":{"cartContainsDonate":{"label":"Warenkorb enthält Donate"}}}}')
    }, R2cx: function (e) {
        e.exports = JSON.parse('{"gabcap-donate":{"general":{"mainMenuItemGeneral":"Donate","descriptionTextModule":"Manage donates here"},"list":{"addButtonText":"Add donate","columnName":"Name","columnDiscountType":"Discount type","columnDiscount":"Discount"},"detail":{"nameLabel":"Name","discountLabel":"Discount","discountTypeLabel":"Discount type","assignProductsLabel":"Assign products","cancelButtonText":"Cancel","saveButtonText":"Save","errorTitle":"Error saving the donate","absoluteText":"Absolute","percentageText":"Percentage"}},"sw-product":{"detail":{"donateCardLabel":"Donates","donateSelectLabel":"Associated donates","donateSelectPlaceholder":"Add donate..."}},"sw-condition":{"condition":{"cartContainsDonate":{"label":"Cart contains donate"}}}}')
    }, XM2Y: function (e, t) {
        e.exports = '{% block sw_condition_base_fields %}\n    <sw-field type="text" class="field--main" size="medium" :disabled="true">\n    </sw-field>\n{% endblock %}'
    }, oRtI: function (e, t) {
        const {Component: n} = Shopware;
        n.override("sw-product-detail", {
            computed: {
                productCriteria() {
                    const e = this.$super("productCriteria");
                    return e.addAssociation("donates"), e
                }
            }
        })
    }
}, [["AyTr", "runtime"]]]);