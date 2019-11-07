## Bitrix Vue Component

```php
<?php
use Dbogdanoff\Bitrix\Vue;

Vue::includeComponent('component-name');
Vue::includeComponent(['component-two', 'component-three']);
```

Структура компонентов:
```php
/*
local/
└── components-vue/
    └── component-one/
        ├── .settings.php
        ├── template.vue
        ├── script.js
        └── style.css
    |── component-two/
    |   └── template.vue
    └── component-three/
        └── template.vue
*/
```
Ни одни из перечисленных файлов компонента не является обязательным, таким образом весь компонент может быть описан в одном script.js файле или в одном template.vue файле. 

В .settings.php могут быть указаны дополнительные зависимости, которые будут автоматически подключены при подключении компонента:
```php
<?
return [
    'require' => [
        'https://unpkg.com/flickity@2.1.2/dist/flickity.pkgd.min.js',
        'https://unpkg.com/flickity@2.1.2/dist/flickity.min.css'
    ]
];
```
При наличии минифицированных стилей или скриптов и установленной соответствующей опции в главном модуле, будут подключены минифицированные файлы.

### Путь к компонентам
По умолчанию поиск компонентов производится в папке /local/components-vue
Данное поведение можно изменить, объявив константу DBOGDANOFF_VUE_PATH
```php
// Компоненты в корне сайта в директории '/components-vue'
define('DBOGDANOFF_VUE_PATH', '/components-vue');
```

### Минификация html-кода всего сайта
Данное решение может минифицировать html-код всего сайта, это плюс к оценке производительности google pagespeed. И имеет удачное применение в сочетании с технологией композитного сайта, так как код кешируется и не производятся строковые операции на каждом хите.
```php
// Активация минификации, возможно регулировать степень soft или hard
define('DBOGDANOFF_VUE_MINIFY', 'hard');
```

В каких случаях следует избегать применения степени сжатия ‘hard’? В тех случаях, когда на странице имеются скрипты, чьи строки не оканчиваются на знак точки с запятой ‘;’ и это может приводить к ошибке SyntaxError. Предварительно следует проанализировать код страницы и выбрать подходящий режим.

![](http://dbogdanoff.ru/upload/github-bitrix-vue-component.png)

### Подключение vue.js
На данный момент, идеология использования подразумевает, что вы самостоятельно подключаете с нужного источника и нужной вам версии Vue.js в header.php шаблона битрикс. Также для разработчика будет уместным подключать не минифицированную версию, для работы с Vue Devtools, следующим образом:
```php
if ($USER->IsAdmin()) {
   Asset::getInstance()->addJs('https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js');
} else {
   Asset::getInstance()->addJs('https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js');
}
```

## Requirements

Bitrix Vue Component requires the following:

- PHP 7.0.0+
- [1C-Bitrix 14.0.0+](https://www.1c-bitrix.ru/)

## Installation

Bitrix Vue Component is installed via [Composer](https://getcomposer.org/).
To [add a dependency](https://getcomposer.org/doc/04-schema.md#package-links>) to bitrix-vue-component in your project, either

Run the following to use the latest stable version
```sh
    composer require denx-b/bitrix-vue-component
```
or if you want the latest master version
```sh
    composer require denx-b/bitrix-vue-component:dev-master
```

You can of course also manually edit your composer.json file
```json
{
    "require": {
       "denx-b/bitrix-vue-component": "0.4.*"
    }
}
```

## Sample component template

``` html
<template id="block-blue">
  <div>Text here...</div>
</template>

<script>
Vue.component('block-blue', {
  template: '#block-blue'
});
</script>
```
----------
**Но лучше один раз увидеть, чем 100500 раз прочитать.**\
*Демо сайт: [https://bitrix-vue-demo.dbogdanoff.ru/](https://bitrix-vue-demo.dbogdanoff.ru/)*\
*Репозиторий сайта: [https://github.com/denx-b/bitrix-vue-component-demo](https://github.com/denx-b/bitrix-vue-component-demo)*
