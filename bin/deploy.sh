#!/bin/bash

rm -rf var/cache/* var/view_preprocessed/* var/page_cache/* pub/static/*
php bin/magento maintenance:enable
#composer update
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f en_US --jobs 4
php bin/magento setup:static-content:deploy -f ru_RU --jobs 4
php bin/magento indexer:reindex
php bin/magento cache:clean
php bin/magento cache:flush
php bin/magento maintenance:disable
