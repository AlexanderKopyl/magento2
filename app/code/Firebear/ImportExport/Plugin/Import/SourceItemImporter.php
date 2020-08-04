<?php

namespace Firebear\ImportExport\Plugin\Import;

use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\Inventory\Model\SourceItem\Command\Handler\SourceItemsSaveHandler;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class SourceItemImporter
 * @package Firebear\ImportExport\Plugin\Import
 */
class SourceItemImporter
{
    /**
     * Source Item Interface Factory
     *
     * @var SourceItemInterfaceFactory $sourceItemFactory
     */
    private $sourceItemFactory;

    /**
     * Default Source Provider
     *
     * @var DefaultSourceProviderInterface $defaultSource
     */
    private $defaultSource;

    /**
     * @var SourceItemsSaveHandler
     */
    protected $sourceItemsSaveHandler;

    /**
     * @param StockItemImporterInterface $subject
     * @param $result
     * @param array $stockData
     * @return mixed
     */
    public function afterImport(
        StockItemImporterInterface $subject,
        $result,
        array $stockData
    ) {
        if (interface_exists(DefaultSourceProviderInterface::class)) {
            $this->defaultSource = ObjectManager::getInstance()
                ->get(DefaultSourceProviderInterface::class);
        }
        if (class_exists(SourceItemInterfaceFactory::class)) {
            $this->sourceItemFactory = ObjectManager::getInstance()
                ->get(SourceItemInterfaceFactory::class);
        }
        if (class_exists(SourceItemsSaveHandler::class)) {
            $this->sourceItemsSaveHandler = ObjectManager::getInstance()
                ->get(SourceItemsSaveHandler::class);
        }
        $sourceItems = [];
        foreach ($stockData as $sku => $stockDatum) {
            $inStock = (isset($stockDatum['is_in_stock'])) ? ((int)$stockDatum['is_in_stock']) : 0;
            $qty = (isset($stockDatum['qty'])) ? $stockDatum['qty'] : 0;
            /** @var SourceItemInterface $sourceItem */
            $sourceItem = $this->sourceItemFactory->create();
            $sourceItem->setSku((string)$sku);
            $sourceItem->setSourceCode($this->defaultSource->getCode());
            $sourceItem->setQuantity((float)$qty);
            $sourceItem->setStatus($inStock);
            $sourceItems[] = $sourceItem;
        }
        if (count($sourceItems) > 0) {
            /** SourceItemInterface[] $sourceItems */
            $this->sourceItemsSaveHandler->execute($sourceItems);
        }

        return $result;
    }
}
