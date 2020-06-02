<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Donate;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateProduct\DonateProductDefinition;
use Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateTranslation\DonateTranslationDefinition;

class DonateDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'gabcap_donate';
    }

    public function getEntityClass(): string
    {
        return DonateEntity::class;
    }

    public function getCollectionClass(): string
    {
        return DonateCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new TranslatedField('name'),
            (new StringField('discount_type', 'discountType'))->addFlags(new Required()),
            (new FloatField('discount', 'discount'))->addFlags(new Required()),
            new TranslationsAssociationField(DonateTranslationDefinition::class, 'gabcap_donate_id'),
            new ManyToManyAssociationField('products', ProductDefinition::class, DonateProductDefinition::class, 'donate_id', 'product_id'),
        ]);
    }
}
