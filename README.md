[![Build Status](https://travis-ci.com/dialect-katrineholm/transedit.svg?token=9jwqzmZxpdyqbmtqpE8y&branch=master)](https://travis-ci.com/dialect-katrineholm/transedit)

# TransEdit
TransEdit stores localizations in a database with built-in cache support. It also has support to enable an edit-mode that allows the user to edit translations directly in the browser by double-clicking the highlighted texts.

## Installation

Install via composer

    composer require dialect/transedit

Publish components

    php artisan vendor:publish --provider="Dialect\TransEdit\TransEditServiceProvider"
    
Add vue component to  ``resources/assets/js/app.js`` and compile
 
    Vue.component('transedit', require('./components/transedit/TransEdit.vue'));

Migrate database
     
     php artisan migrate
     
## Publishes

    Assets => resource/assets
    Config => config/
    Migrations database/migrations/

## Example

``` php
       ##Locale##
       //A locale is automatically created when a key is set or if a locale language name is set.
       transEdit()->setLocaleLanguageName('locale', 'language-name'); //e.g en, English
        
       ##set key##
       transEdit()->setKey('key', 'val');
       transEdit()->locale('locale')->setKey(..); //TransEdit automatically creates missing locales.
       
       //you can use the helper functions to quicker set keys
       transEdit()->key('key', 'value');
       transEdit('key', 'val', 'locale'); //locale is optional
       
       ##get key##
       transEdit()->getKey('key');
       transEdit()->locale('locale')->getKey('key');
       
       //you can use the helper functions to quicker get keys
       transEdit()->key('key');
       transEdit('key');
        
       ##set current locale for whole system to use##
       transEdit()->setCurrentLocale('locale');
       
       ##Enable/Disable edit-mode
       transEdit()->enableEditMode();
       transEdit()->disableEditMode();
       
       //When you set locale or edit-mode it will only be set for the current session. 
       //That means multiple users can have different settings.
       
       
```

## Artisan Command

Adds all Laravel language files from the lang/ folder into transedit:
```
transedit:addLangFilesToDatabase
```
Ex. lang/sv/article.php
```
<?php

return [
	"recipe" => "Recept",
];
```
When added to transedit, it can be reached for with the key transedit('article.recipe'), 
just like you would use Laravel's own translation: @lang("article.recipe")
