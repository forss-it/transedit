# TransEdit
TransEdit stores localizations in a database with built-in cache support. It also has support to enable a edit-mode that allows the user to edit translations directly in the web-browser by double-clicking the highlighted texts.

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

    Controller => app/http/contollers/
    Config => config/
    Migrations database/migrations/

## Example

``` php
       ##Locale##
       //A locale is automatically created when a key is set or if a locale language name is set.
       transEdit()->setLocaleLanguageName('locale', 'language-name'); //e.g en, English
        
       ##set key##
       
       transEdit()->setKey('key', 'val');
       transEdit()->locale('locale')->setKey(..); //TransEdit automatically creates missing locale.
       
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

