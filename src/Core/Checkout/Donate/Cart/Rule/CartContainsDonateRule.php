<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Checkout\Donate\Cart\Rule;

use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Gabcap\DonateExample\Core\Checkout\Donate\Cart\DonateCartProcessor;

class CartContainsDonateRule extends Rule
{
    public function getName(): string
    {
        return 'gabcapDonateContainsDonate';
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CartRuleScope) {
            return false;
        }

        $donates = $scope->getCart()->getLineItems()->filterFlatByType(DonateCartProcessor::TYPE);

        if (\count($donates) < 1) {
            return false;
        }

        return true;
    }

    public function getConstraints(): array
    {
        return [];
    }
}
