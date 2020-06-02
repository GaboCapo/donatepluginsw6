<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Checkout\Donate\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Gabcap\DonateExample\Core\Content\Donate\DonateCollection;
use Gabcap\DonateExample\Core\Content\Donate\DonateEntity;

class DonateCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
{
    public const TYPE = 'gabcapdonate';
    public const DISCOUNT_TYPE = 'gabcapdonate-discount';
    public const DATA_KEY = 'gabcap_donate-';
    public const DISCOUNT_TYPE_ABSOLUTE = 'absolute';
    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    /**
     * @var EntityRepositoryInterface
     */
    private $donateRepository;

    /**
     * @var PercentagePriceCalculator
     */
    private $percentagePriceCalculator;

    /**
     * @var AbsolutePriceCalculator
     */
    private $absolutePriceCalculator;

    /**
     * @var QuantityPriceCalculator
     */
    private $quantityPriceCalculator;

    public function __construct(
        EntityRepositoryInterface $donateRepository,
        PercentagePriceCalculator $percentagePriceCalculator,
        AbsolutePriceCalculator $absolutePriceCalculator,
        QuantityPriceCalculator $quantityPriceCalculator
    ) {
        $this->donateRepository = $donateRepository;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        /** @var LineItemCollection $donateLineItems */
        $donateLineItems = $original->getLineItems()->filterType(self::TYPE);

        // no donates in cart? exit
        if (\count($donateLineItems) === 0) {
            return;
        }

        // fetch missing donate information from database
        $donates = $this->fetchDonates($donateLineItems, $data, $context);

        foreach ($donates as $donate) {
            // ensure all line items have a data entry
            $data->set(self::DATA_KEY . $donate->getId(), $donate);
        }

        foreach ($donateLineItems as $donateLineItem) {
            $donate = $data->get(self::DATA_KEY . $donateLineItem->getReferencedId());

            // enrich donate information with quantity and delivery information
            $this->enrichDonate($donateLineItem, $donate);

            // add donate products which are not already assigned
            $this->addMissingProducts($donateLineItem, $donate);

            // add donate discount if not already assigned
            $this->addDiscount($donateLineItem, $donate, $context);
        }
    }

    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // collect all donate in cart
        /** @var LineItemCollection $donateLineItems */
        $donateLineItems = $original->getLineItems()
            ->filterType(self::TYPE);

        if (\count($donateLineItems) === 0) {
            return;
        }

        foreach ($donateLineItems as $donateLineItem) {
            // first calculate all donate product prices
            $this->calculateChildProductPrices($donateLineItem, $context);

            // after the product prices calculated, we can calculate the discount
            $this->calculateDiscountPrice($donateLineItem, $context);

            // at last we have to set the total price for the root line item (the donate)
            $donateLineItem->setPrice(
                $donateLineItem->getChildren()->getPrices()->sum()
            );

            // afterwards we can move the donate to the new cart
            $toCalculate->add($donateLineItem);
        }
    }

    /**
     * Fetches all Donates that are not already stored in data
     */
    private function fetchDonates(LineItemCollection $donateLineItems, CartDataCollection $data, SalesChannelContext $context): DonateCollection
    {
        $donateIds = $donateLineItems->getReferenceIds();

        $filtered = [];
        foreach ($donateIds as $donateId) {
            // If data already contains the donate we don't need to fetch it again
            if ($data->has(self::DATA_KEY . $donateId)) {
                continue;
            }

            $filtered[] = $donateId;
        }

        $criteria = new Criteria($filtered);
        $criteria->addAssociation('products');
        /** @var DonateCollection $donates */
        $donates = $this->donateRepository->search($criteria, $context->getContext())->getEntities();

        return $donates;
    }

    private function enrichDonate(LineItem $donateLineItem, DonateEntity $donate): void
    {
        if (!$donateLineItem->getLabel()) {
            $donateLineItem->setLabel($donate->getName());
        }

        $donateProducts = $donate->getProducts();
        if ($donateProducts === null) {
            throw new \RuntimeException(sprintf('Donate "%s" has no products', $donate->getName()));
        }

        $firstDonateProduct = $donateProducts->first();
        if ($firstDonateProduct === null) {
            throw new \RuntimeException(sprintf('Donate "%s" has no products', $donate->getName()));
        }

        $firstDonateProductDeliveryTime = $firstDonateProduct->getDeliveryTime();
        if ($firstDonateProductDeliveryTime !== null) {
            $firstDonateProductDeliveryTime = DeliveryTime::createFromEntity($firstDonateProductDeliveryTime);
        }

        $donateLineItem->setRemovable(true)
            ->setStackable(true)
            ->setDeliveryInformation(
                new DeliveryInformation(
                    $firstDonateProduct->getStock(),
                    (float) $firstDonateProduct->getWeight(),
                    (bool) $firstDonateProduct->getShippingFree(),
                    $firstDonateProduct->getRestockTime(),
                    $firstDonateProductDeliveryTime
                )
            )
            ->setQuantityInformation(new QuantityInformation());
    }

    private function addMissingProducts(LineItem $donateLineItem, DonateEntity $donate): void
    {
        $donateProducts = $donate->getProducts();
        if ($donateProducts === null) {
            throw new \RuntimeException(sprintf('Donate %s has no products', $donate->getName()));
        }

        foreach ($donateProducts->getIds() as $productId) {
            // if the donateLineItem already contains the product we can skip it
            if ($donateLineItem->getChildren()->has($productId)) {
                continue;
            }

            // the ProductCartProcessor will enrich the product further
            $productLineItem = new LineItem($productId, LineItem::PRODUCT_LINE_ITEM_TYPE, $productId);

            $donateLineItem->addChild($productLineItem);
        }
    }

    private function addDiscount(LineItem $donateLineItem, DonateEntity $donate, SalesChannelContext $context): void
    {
        if ($this->getDiscount($donateLineItem)) {
            return;
        }

        $discount = $this->createDiscount($donate, $context);

        if ($discount) {
            $donateLineItem->addChild($discount);
        }
    }

    private function getDiscount(LineItem $donate): ?LineItem
    {
        return $donate->getChildren()->get($donate->getReferencedId() . '-discount');
    }

    private function createDiscount(DonateEntity $donateData, SalesChannelContext $context): ?LineItem
    {
        if ($donateData->getDiscount() === 0.0) {
            return null;
        }

        switch ($donateData->getDiscountType()) {
            case self::DISCOUNT_TYPE_ABSOLUTE:
                $priceDefinition = new AbsolutePriceDefinition($donateData->getDiscount() * -1, $context->getContext()->getCurrencyPrecision());
                $label = 'Absolute donate voucher';
                break;

            case self::DISCOUNT_TYPE_PERCENTAGE:
                $priceDefinition = new PercentagePriceDefinition($donateData->getDiscount() * -1, $context->getContext()->getCurrencyPrecision());
                $label = sprintf('Spendenwert', $donateData->getDiscount());
                break;

            default:
                throw new \RuntimeException('Invalid discount type.');
        }

        $discount = new LineItem(
            $donateData->getId() . '-discount',
            self::DISCOUNT_TYPE,
            $donateData->getId()
        );

        $discount->setPriceDefinition($priceDefinition)
            ->setLabel($label)
            ->setGood(false);

        return $discount;
    }

    private function calculateChildProductPrices(LineItem $donateLineItem, SalesChannelContext $context): void
    {
        /** @var LineItemCollection $products */
        $products = $donateLineItem->getChildren()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

        foreach ($products as $product) {
            $priceDefinition = $product->getPriceDefinition();
            if ($priceDefinition === null || !$priceDefinition instanceof QuantityPriceDefinition) {
                throw new \RuntimeException(sprintf('Product "%s" has invalid price definition', $product->getLabel()));
            }

            $product->setPrice(
                $this->quantityPriceCalculator->calculate($priceDefinition, $context)
            );
        }
    }

    private function calculateDiscountPrice(LineItem $donateLineItem, SalesChannelContext $context): void
    {
        $discount = $this->getDiscount($donateLineItem);

        if (!$discount) {
            return;
        }

        $childPrices = $donateLineItem->getChildren()
            ->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE)
            ->getPrices();

        $priceDefinition = $discount->getPriceDefinition();

        if (!$priceDefinition) {
            return;
        }

        switch (\get_class($priceDefinition)) {
            case AbsolutePriceDefinition::class:
                $price = $this->absolutePriceCalculator->calculate(
                    $priceDefinition->getPrice(),
                    $childPrices,
                    $context,
                    $donateLineItem->getQuantity()
                );
                break;

            case PercentagePriceDefinition::class:
                $price = $this->percentagePriceCalculator->calculate(
                    $priceDefinition->getPercentage(),
                    $childPrices,
                    $context
                );
                break;

            default:
                throw new \RuntimeException('Invalid discount type.');
        }

        $discount->setPrice($price);
    }
}
