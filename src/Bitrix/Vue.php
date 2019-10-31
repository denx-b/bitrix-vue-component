<?

namespace Dbogdanoff\Bitrix;

use Bitrix\Main\Page\Asset;
use Bitrix\Main\ModuleManager;

class Vue
{
    const COMPONENTS_PATH = '/local/components-vue';

    protected static $init = false;
    protected static $arHtml = [];
    protected static $arIncluded = [];

    /**
     * Подключает Vue-компонет
     *
     * @param string|array $componentName
     * @param array $addFiles
     * @throws \Exception
     */
    public static function includeComponent($componentName, array $addFiles = [])
    {
        if (self::$init !== true) {
            self::checkBitrix();
            self::$init = true;
            \AddEventHandler('main', 'OnEndBufferContent', ['\Dbogdanoff\Bitrix\Vue', 'insertComponents']);
        }

        foreach ((array)$componentName as $name) {
            if (self::$arIncluded[$name] !== true) {
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

                if (file_exists($rootPath . '/' . $name . '/script.js')) {
                    self::addFile($docPath . '/' . $name . '/script.js');
                }

                if (file_exists($rootPath . '/' . $name . '/style.css')) {
                    self::addFile($docPath . '/' . $name . '/style.css');
                }
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
        $content = preg_replace('/<body([^>])>/', "<body$1>" . $include, $content, 1);
        if (
            defined('DBOGDANOFF_VUE_REPLACE_DOUBLE_EOL') &&
            strpos($_SERVER['REQUEST_URI'], '/bitrix') === false &&
            strpos($_SERVER['REQUEST_URI'], '/local') === false &&
            strpos($_SERVER['REQUEST_URI'], '/api') === false &&
            !preg_match('/.*\.(pdf|png|jpg|jpeg|gif|webp|exe)/i', $_SERVER['REQUEST_URI']) &&
            $GLOBALS['APPLICATION']->PanelShowed !== true
        ) {
            $content = self::replaceDoubleEol($content);
        }
    }

    protected static function replaceDoubleEol(&$content): string
    {
        $arReplace = [
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => ' <',
            '/(\s)+/s' => '\\1'
        ];

        $content = preg_replace(array_keys($arReplace), $arReplace, $content);
        return trim($content);
    }

    /**
     * Инициализирует глобальные настройки, доступные в компонентах this.$bx
     * @return string
     */
    protected static function getGlobalJsConfig(): string
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
    protected static function getComponentsPath(): string
    {
        return defined('DBOGDANOFF_VUE_PATH') ? '/' . trim(DBOGDANOFF_VUE_PATH, '/') : self::COMPONENTS_PATH;
    }

    /**
     * Проверка подключения пролога и версии ядра
     * @throws \Exception
     */
    protected static function checkBitrix()
    {
        if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
            throw new \Exception('Bitrix not found');
        }

        if (!\CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            throw new \Exception('Current edition does not support D7');
        }
    }
}
