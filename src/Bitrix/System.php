<?php

namespace Dbogdanoff\Bitrix;

use Exception;
use Bitrix\Main\ModuleManager;

class System
{
    /**
     * Проверка подключения пролога и версии ядра
     * @throws Exception
     */
    public static function checkBitrix()
    {
        if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
            throw new Exception('Bitrix not found');
        }

        if (!\CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            throw new Exception('Current edition does not support D7');
        }
    }

    /**
     * Минификация html-кода
     *
     * @param string $content
     * @param string $rate степень минификации hard|soft
     * @return string
     */
    public static function minifyContent(string $content, string $rate = 'soft'): string
    {
        $arReplace = ['/(\s)+/s' => '\\1'];

        if (strtolower($rate) === 'hard') {
            $arReplace['/\>[^\S ]+/s'] = '>';
            $arReplace['/[^\S ]+\</s'] = ' <';
        }

        $content = preg_replace(array_keys($arReplace), $arReplace, $content);
        return trim($content);
    }
}
