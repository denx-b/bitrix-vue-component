<?
namespace Dbogdanoff\Bitrix;

use Bitrix\Main\Page\Asset;
use Bitrix\Main\ModuleManager;

class Vue
{
    const VERSION = '2.5.17';
    const COMPONENTS_PATH = '/local/components-vue';

    protected static $inited = false;
    protected static $arHtml = [];
    protected static $arIncluded = [];

    /**
     * @param string $name
     * @throws \Exception
     */
    public static function includeComponent(string $name) {
        if (self::$inited !== true) {
            self::checkBitrix();
            self::$inited = true;
            Asset::getInstance()->addJs( self::getCdnPath() );
            \AddEventHandler('main', 'OnEndBufferContent', ['\Dbogdanoff\Bitrix\Vue', 'changeMyContent']);
        }

        $name = preg_replace('/\s+/', '', $name);
        if (self::$arIncluded[ $name ]) {
            return true;
        }
        else {
            self::$arIncluded[ $name ] = true;
        }

        $docPath    = self::getComponentsPath();
        $rootPath   = $_SERVER['DOCUMENT_ROOT'] . $docPath;

        if (file_exists($template = $rootPath .'/'. $name .'/template.vue')) {
            self::$arHtml[] = file_get_contents($template);
        }

        if (file_exists($rootPath .'/'. $name .'/script.js')) {
            Asset::getInstance()->addJs( $docPath .'/'. $name .'/script.js');
        }

        if (file_exists($rootPath .'/'. $name .'/style.css')) {
            Asset::getInstance()->addCss( $docPath .'/'. $name .'/style.css');
        }

        return true;
    }

    public static function changeMyContent(&$content) {
        $include  = "";
        $include .= "<div style='display:none'>\n";
        $include .= implode("\n", self::$arHtml);
        $include .= "</div>\n";
        $content = str_replace('</body>', $include .'</body>', $content);
    }

    protected static function getComponentsPath(): string {
        if (defined('DBOGDANOFF_VUE_PATH')) {
            return '/'. trim(DBOGDANOFF_VUE_PATH, '/');
        }

        return self::COMPONENTS_PATH;
    }

    /**
     * Возвращает путь для подключения Vue.js
     * @return string
     */
    protected static function getCdnPath(): string {
        $min        = defined('DBOGDANOFF_VUE_DEBUG') ? '' : '.min';
        $version    = defined('DBOGDANOFF_VUE_VERSION') ? DBOGDANOFF_VUE_VERSION : self::VERSION;

        return sprintf('https://cdnjs.cloudflare.com/ajax/libs/vue/%s/vue%s.js', $version, $min);
    }

    /**
     * @throws \Exception
     */
    protected static function checkBitrix() {
        if (self::checkBitrixFramework() !== true) {
            throw new \Exception('Bitrix not found');
        }

        if (self::checkBitrixVersion() !== true) {
            throw new \Exception('Current edition does not support D7');
        }
    }

    /**
     * @return bool
     */
    protected static function checkBitrixFramework(): bool {
        return defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true;
    }

    /**
     * @return bool
     */
    protected static function checkBitrixVersion(): bool {
        return \CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }
}
