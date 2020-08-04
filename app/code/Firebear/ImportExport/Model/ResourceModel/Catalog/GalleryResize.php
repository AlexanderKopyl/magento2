<?php

namespace Firebear\ImportExport\Model\ResourceModel\Catalog;

use Magento\Framework\App\ResourceConnection;

/**
 * Class GalleryReindex
 * @package Firebear\ImportExport\Model\ResourceModel\Catalog
 */
class GalleryResize
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * CatalogEmail constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param $rows
     * @return array
     */
    public function getImages($rows)
    {
        $conn = $this->resourceConnection->getConnection();
        $query = $conn->select()
            ->from(['cpe' => $conn->getTableName('catalog_product_entity')], 'cpe.sku AS sku')
            ->joinLeft(
                ['cpegve' => $conn->getTableName('catalog_product_entity_media_gallery_value_to_entity')],
                'cpegve.entity_id=cpe.entity_id'
            )->join(
                ['cpeg' => $conn->getTableName('catalog_product_entity_media_gallery')],
                'cpeg.value_id=cpegve.value_id',
                'cpeg.value AS value'
            )
            ->where('cpe.entity_id IN(?)', array_unique(array_column($rows, 'entity_id')));

        return $conn->fetchAll($query);
    }
}
