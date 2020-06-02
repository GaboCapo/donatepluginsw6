<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Gabcap\DonateExample\Core\Content\Donate\DonateDefinition;

class DonateTranslationDefinition extends EntityTranslationDefinition
{
    public function getEntityName(): string
    {
        return 'gabcap_donate_translation';
    }

    public function getCollectionClass(): string
    {
        return DonateTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return DonateTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return DonateDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
        ]);
    }
}
