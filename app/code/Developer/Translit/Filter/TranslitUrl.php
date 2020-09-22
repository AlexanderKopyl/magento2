<?php

declare(strict_types=1);

namespace Developer\Translit\Filter;

class TranslitUrl extends \Magento\Framework\Filter\TranslitUrl
{
    /**
     * Add correct Russian transliteration
     *
     * @return array
     */
    protected function getConvertTable()
    {
        $convertTable = $this->convertTable;

        $convertTable['ЬЕ'] = 'je';
        $convertTable['ье'] = 'je';
        $convertTable['ЬЯ'] = 'ya';
        $convertTable['ья'] = 'ya';
        $convertTable['ЬЁ'] = 'jo';
        $convertTable['ьё'] = 'jo';
        $convertTable['ЬЮ'] = 'ju';
        $convertTable['ью'] = 'ju';
        $convertTable['ЪЕ'] = 'je';
        $convertTable['ъе'] = 'je';
        $convertTable['ЪЯ'] = 'ja';
        $convertTable['ъя'] = 'ja';
        $convertTable['ЪЁ'] = 'jo';
        $convertTable['ъё'] = 'jo';
        $convertTable['ЪЮ'] = 'ju';
        $convertTable['ъю'] = 'ju';

        return $convertTable;
    }
}
