<?php

namespace Dbogdanoff\Bitrix;

use Exception;
use Bitrix\Main\Page\Asset;

class Vue
{
    const COMPONENTS_PATH = '/local/components-vue';

    protected static $init = false;
    protected static $arHtml = [];
    protected static $arIncluded = [];

    /**
     * Подключает Vue-компонент
     *
     * @param string|array $componentName
     * @param array $addFiles
     * @throws Exception
     */
    public static function includeComponent($componentName, array $addFiles = [])
    {
        if (self::$init !== true) {
            System::checkBitrix();
            self::$init = true;

            // Подключаем Vue.js и Vuex
            if (defined('DBOGDANOFF_ADD_JS') && DBOGDANOFF_ADD_JS === true) {
                Asset::getInstance()->addJs('https://unpkg.com/vuex@3.5.1/dist/vuex' . (!defined('DBOGDANOFF_DEV') ? '.min' : '') . '.js');
                Asset::getInstance()->addJs('https://unpkg.com/vue@2.6.11/dist/vue' . (!defined('DBOGDANOFF_DEV') ? '.min' : '') . '.js');
            }

            \AddEventHandler('main', 'OnEndBufferContent', ['\Dbogdanoff\Bitrix\Vue', 'insertComponents']);
        }

        foreach ((array)$componentName as $name) {
            if (self::$arIncluded[$name] === true) {
                continue;
            }

            self::$arIncluded[$name] = true;

            $docPath = self::getComponentsPath();
            $rootPath = $_SERVER['DOCUMENT_ROOT'] . $docPath;

            // Подключает зависимости скрипты/стили
            if (file_exists($settings = $rootPath . '/' . $name . '/.settings.php')) {
                $settings = require_once $settings;
                if (array_key_exists('require', $settings)) {
                    foreach ((array)$settings['require'] as $file) {
                        self::addFile($file);
                    }
                }
            }

            // Подключает доп. зависимости скрипты/стили
            foreach ($addFiles as $file) {
                self::addFile($file);
            }

            if (file_exists($template = $rootPath . '/' . $name . '/template.vue')) {
                self::$arHtml[] = file_get_contents($template);
            }

            if (!defined('DBOGDANOFF_DEV') && file_exists($rootPath . '/' . $name . '/script.min.js')) {
                self::addFile($docPath . '/' . $name . '/script.min.js');
            } elseif (file_exists($rootPath . '/' . $name . '/script.js')) {
                self::addFile($docPath . '/' . $name . '/script.js');
            } elseif (file_exists($rootPath . '/' . $name . '/script.min.js')) {
                self::addFile($docPath . '/' . $name . '/script.min.js');
            }

            if (!defined('DBOGDANOFF_DEV') && file_exists($rootPath . '/' . $name . '/style.min.css')) {
                self::addFile($docPath . '/' . $name . '/style.min.css');
            } elseif (file_exists($rootPath . '/' . $name . '/style.css')) {
                self::addFile($docPath . '/' . $name . '/style.css');
            } elseif (file_exists($rootPath . '/' . $name . '/style.min.css')) {
                self::addFile($docPath . '/' . $name . '/style.min.css');
            }
        }
    }

    /**
     * Подключает js или css файл
     *
     * @param string $file
     */
    public static function addFile(string $file)
    {
        global $APPLICATION;

        if (strpos($file, '.js') !== false) {
            Asset::getInstance()->addJs($file);
        } else {
            if (strpos($file, '.css') !== false) {
                $APPLICATION->SetAdditionalCSS($file);
            }
        }
    }

    /**
     * Вставляет все подключенные компоненты в тело документа
     * Метод обработчик события OnEndBufferContent
     *
     * @param $content
     */
    public static function insertComponents(&$content)
    {
        $include = "<div style='display:none'>";
        $include .= implode("\n", self::$arHtml);
        $include .= self::getGlobalJsConfig();
        $include .= "</div>";
        $content = preg_replace('/<body([^>]*)?>/', "<body$1>" . $include, $content, 1);
        if (
            defined('DBOGDANOFF_VUE_MINIFY') &&
            strpos($_SERVER['REQUEST_URI'], '/bitrix') === false &&
            strpos($_SERVER['REQUEST_URI'], '/local') === false &&
            strpos($_SERVER['REQUEST_URI'], '/rest') === false &&
            strpos($_SERVER['REQUEST_URI'], '/api') === false &&
            !preg_match('/.*\.(pdf|png|jpg|jpeg|gif|webp|exe)/i', $_SERVER['REQUEST_URI']) &&
            $GLOBALS['APPLICATION']->PanelShowed !== true
        ) {
            $content = System::minifyContent($content, DBOGDANOFF_VUE_MINIFY);
        }
    }

    /**
     * Инициализирует глобальные настройки, доступные в компонентах this.$bx
     * @return string
     */
    public static function getGlobalJsConfig(): string
    {
        $script = '<script>';
        $script .= 'Vue.prototype.$bx=';
        $script .= json_encode([
            'componentsPath' => self::getComponentsPath(),
            'siteTemplatePath' => SITE_TEMPLATE_PATH
        ]);
        $script .= '</script>';

        return $script;
    }

    /**
     * Путь к директории с компонентами
     * @return string
     */
    public static function getComponentsPath(): string
    {
        if (defined('DBOGDANOFF_VUE_PATH')) {
            return '/' . trim(DBOGDANOFF_VUE_PATH, '/');
        }

        return self::COMPONENTS_PATH;
    }
}
