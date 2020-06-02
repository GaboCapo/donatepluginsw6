<?php declare(strict_types=1);

namespace Gabcap\DonateExample\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1554708925Donate extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1554708925;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `gabcap_donate` (
              `id` BINARY(16) NOT NULL,
              `discount_type` VARCHAR(255) NOT NULL,
              `discount` DOUBLE NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `gabcap_donate_translation` (
              `gabcap_donate_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `name` VARCHAR(255),
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`gabcap_donate_id`, `language_id`),
              CONSTRAINT `fk.donate_translation.donate_id` FOREIGN KEY (`gabcap_donate_id`)
                REFERENCES `gabcap_donate` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.donate_translation.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `gabcap_donate_product` (
              `donate_id` BINARY(16) NOT NULL,
              `product_id` BINARY(16) NOT NULL,
              `product_version_id` BINARY(16) NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              PRIMARY KEY (`donate_id`, `product_id`, `product_version_id`),
              CONSTRAINT `fk.donate_product.donate_id` FOREIGN KEY (`donate_id`)
                REFERENCES `gabcap_donate` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.donate_product.product_id__product_version_id` FOREIGN KEY (`product_id`, `product_version_id`)
                REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $this->updateInheritance($connection, 'product', 'donates');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
