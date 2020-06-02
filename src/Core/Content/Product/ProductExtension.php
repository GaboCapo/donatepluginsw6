<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateProduct\DonateProductDefinition;
use Gabcap\DonateExample\Core\Content\Donate\DonateDefinition;

class ProductExtension implements EntityExtensionInterface
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'donates',
                DonateDefinition::class,
                DonateProductDefinition::class,
                'product_id',
                'donate_id'
            ))->addFlags(new Inherited())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
