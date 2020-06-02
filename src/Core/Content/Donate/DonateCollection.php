<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Donate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(DonateEntity $entity)
 * @method void              set(string $key, DonateEntity $entity)
 * @method DonateEntity[]    getIterator()
 * @method DonateEntity[]    getElements()
 * @method DonateEntity|null get(string $key)
 * @method DonateEntity|null first()
 * @method DonateEntity|null last()
 */
class DonateCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DonateEntity::class;
    }
}
