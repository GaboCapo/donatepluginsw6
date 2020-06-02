<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(DonateTranslationEntity $entity)
 * @method void                         set(string $key, DonateTranslationEntity $entity)
 * @method DonateTranslationEntity[]    getIterator()
 * @method DonateTranslationEntity[]    getElements()
 * @method DonateTranslationEntity|null get(string $key)
 * @method DonateTranslationEntity|null first()
 * @method DonateTranslationEntity|null last()
 */
class DonateTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DonateTranslationEntity::class;
    }
}
