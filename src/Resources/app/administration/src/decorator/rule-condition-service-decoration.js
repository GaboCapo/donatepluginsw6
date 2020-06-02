import '../core/component/gabcap-cart-contains-donate';

const { Application } = Shopware;

Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('gabcapDonateContainsDonate', {
        component: 'gabcap-cart-contains-donate',
        label: 'sw-condition.condition.cartContainsDonate.label',
        scopes: ['cart']
    });

    return ruleConditionService;
});
