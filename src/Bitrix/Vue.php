<?
namespace Dbogdanoff\Bitrix;

use Bitrix\Main\Page\Asset;
use Bitrix\Main\ModuleManager;

class Vue
{
    const COMPONENTS_PATH = '/local/components-vue';

    protected static $inited = false;
    protected static $arHtml = [];
    protected static $arIncluded = [];

    /**
     * @param string $name
     * @param array $addFiles
     * @throws \Exception
     */
    public static function includeComponent(string $name, array $addFiles = []) {
        if (self::$inited !== true) {
            self::checkBitrix();
            self::$inited = true;
            \AddEventHandler('main', 'OnEndBufferContent', ['\Dbogdanoff\Bitrix\Vue', 'insertComponents']);
        }

        if (self::$arIncluded[ $name ] !== true) {
            self::$arIncluded[ $name ] = true;

            $docPath    = self::getComponentsPath();
            $rootPath   = $_SERVER['DOCUMENT_ROOT'] . $docPath;

            if (file_exists($template = $rootPath .'/'. $name .'/template.vue')) {
                self::$arHtml[] = file_get_contents($template);
            }

            // Подключает зависимости скрипты и стили
            if (file_exists($settings = $rootPath .'/'. $name .'/.settings.php')) {
                $settings = require_once $settings;
                if (is_array($settings['require'])) {
                    foreach($settings['require'] as $file) {
                        self::addFile($file);
                    }
                }
            }

            // Подключает доп. зависимости скрипты и стили
            foreach($addFiles as $file) {
                self::addFile($file);
            }

            if (file_exists($rootPath .'/'. $name .'/script.js')) {
                self::addFile($docPath .'/'. $name .'/script.js');
            }

            if (file_exists($rootPath .'/'. $name .'/style.css')) {
                self::addFile($docPath .'/'. $name .'/style.css');
            }
        }
    }

    public static function addFile(string $file) {
        if (strpos($file, 'js') !== false) {
            Asset::getInstance()->addJs($file);
        }
        else if (strpos($file, 'css') !== false) {
            Asset::getInstance()->addCss($file);
        }
    }

    /**
     * Вставка компонентов.
     * Метод обработчик события OnEndBufferContent
     *
     * @param $content
     */
    public static function insertComponents(&$content) {
        $include = "<div style='display:none'>\n". implode("\n", self::$arHtml) ."</div>\n";
        $content = str_replace("<body>", "<body>\n". $include, $content);
    }

    /**
     * Возвращает путь к директории с компонентами
     * @return string
     */
    protected static function getComponentsPath(): string {
        return defined('DBOGDANOFF_VUE_PATH') ? '/'. trim(DBOGDANOFF_VUE_PATH, '/') : self::COMPONENTS_PATH;
    }

    /**
     * Проверка подключения пролога и версии ядра
     * @throws \Exception
     */
    protected static function checkBitrix() {
        if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
            throw new \Exception('Bitrix not found');
        }

        if (!\CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            throw new \Exception('Current edition does not support D7');
        }
    }
}
