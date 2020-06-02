<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Core\Content\Donate\Aggregate\DonateTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Gabcap\DonateExample\Core\Content\Donate\DonateEntity;

class DonateTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $donateId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var DonateEntity
     */
    protected $donate;

    public function getDonateId(): string
    {
        return $this->donateId;
    }

    public function setDonateId(string $donateId): void
    {
        $this->donateId = $donateId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDonate(): DonateEntity
    {
        return $this->donate;
    }

    public function setDonate(DonateEntity $donate): void
    {
        $this->donate = $donate;
    }
}
