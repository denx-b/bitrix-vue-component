## Bitrix Vue Component

```php
<?php
use Dbogdanoff\Bitrix\Vue;

// string name
Vue::includeComponent('component-name');

// array names
Vue::includeComponent([
    'block-header',
    'block-footer',
    'dbogdanoff-loader',
    'dbogdanoff-popup'
]);
```

Your App:
```html
<div id="app">
  <block-header></block-header>
  <block-footer></block-footer>
  <dbogdanoff-loader></dbogdanoff-loader>
  <dbogdanoff-popup></dbogdanoff-popup>
  ...
</div>
```

Default directory structure:
```php
/*
/local/components-vue
/local/components-vue/component-name
/local/components-vue/component-name/.settins.php
/local/components-vue/component-name/template.vue
/local/components-vue/component-name/script.js
/local/components-vue/component-name/style.css
*/
```

## Requirements

Bitrix Vue Compont requires the following:

- PHP 7.0.0+
- [1C-Bitrix 14.0.0+](https://www.1c-bitrix.ru/)

## Installation

Bitrix Vue Compont is installed via [Composer](https://getcomposer.org/).
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

## Sample componet

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
*Компонент может содержать следующие файлы:*

     - .settings.php
     - template.vue
     - script.js
     - style.css

Ни одни из файлов не является обязательным, таким образом весь компонент может быть описан в одном script.js файле или в одном template.vue файле. 

При наличии минифицированных стилей или скриптов и установленной соответствующей опции в главном модуле, будут подключены минифицированные файлы.

```php
<?
/*
В .setting.php могут быть указаны доп. зависимости компонента
которые будут автоматически подключены при подключении компонента
*/
return [
    'require' => [
        'https://unpkg.com/flickity@2.1.2/dist/flickity.pkgd.min.js',
        'https://unpkg.com/flickity@2.1.2/dist/flickity.min.css'
    ]
];
```
По умолчанию поиск компонентов производится в папке /local/components-vue
Данное поведение можно изменить, объявив константу DBOGDANOFF_VUE_PATH
```php
// компоненты в корне сайта в директории 'components-vue'
define('DBOGDANOFF_VUE_PATH', '/components-vue');
```

----------
**Но лучше один раз увидеть, чем 100500 раз прочитать *(чуть позже)*.**\
*Демо сайт [https://example.com](https://example.com)*\
*Репозиторий сайта: [https://example.com](https://example.com)*
