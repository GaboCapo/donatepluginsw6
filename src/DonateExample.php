<?php declare(strict_types=1);

namespace Gabcap\DonateExample;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\Indexer\InheritanceIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\MessageQueue\IndexerMessageSender;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class DonateExample extends Plugin
{
    public function activate(ActivateContext $activateContext): void
    {
        $indexerMessageSender = $this->container->get(IndexerMessageSender::class);
        $indexerMessageSender->partial(new \DateTimeImmutable(), [InheritanceIndexer::getName()]);
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);

        $connection->executeUpdate('DROP TABLE IF EXISTS `gabcap_donate_product`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `gabcap_donate_translation`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `gabcap_donate`');
        $connection->executeUpdate('ALTER TABLE `product` DROP COLUMN `donates`');
    }
}
