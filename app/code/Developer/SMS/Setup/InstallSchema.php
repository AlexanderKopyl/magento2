<?php

namespace Developer\SMS\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if ($connection->tableColumnExists('sales_order', 'is_send_puls_send') === false) {
            $orderTable = $installer->getTable('sales_order');
            $connection->addColumn(
                $orderTable,
                'is_send_puls_send',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 1,
                    'comment' => 'Flag for SendPuls is sent on new order'
                ]
            );
        }
        $installer->endSetup();
    }
}
