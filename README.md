## Bitrix Vue Component

```php
<?php
use Dbogdanoff\Bitrix\Vue;
Vue::includeComponent('component-name');
```

Структура компонентов:
```php
/*
local/
└── components-vue/
    |── component-one/
    |   └── template.vue
    |── component-two/
    |   └── template.vue
    └── component-three/
        ├── .settings.php
        ├── template.vue
        ├── script.js
        └── style.css
*/
```
Ни одни из перечисленных файлов компонента не является обязательным, таким образом весь компонент может быть описан в одном script.js файле или в одном template.vue файле. 

В .setting.php могут быть указаны дополнительные зависимости, которые будут автоматически подключены при подключении компонента:
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

По умолчанию поиск компонентов производится в папке /local/components-vue
Данное поведение можно изменить, объявив константу DBOGDANOFF_VUE_PATH
```php
// компоненты в корне сайта в директории 'components-vue'
define('DBOGDANOFF_VUE_PATH', '/components-vue');
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
