Below is an **expanded README** that includes details on how the `add-missing-phrases` command can optionally create a migration file or push phrases directly to the database.

---

[![Build Status](https://travis-ci.com/dialect-katrineholm/transedit.svg?token=9jwqzmZxpdyqbmtqpE8y&branch=master)](https://travis-ci.com/dialect-katrineholm/transedit)

# TransEdit

**TransEdit** is a Laravel plugin designed to store and manage localization strings in a database with built-in caching. It offers a flexible and interactive way to handle language translations, including an optional *edit mode*, which allows users to edit translations directly in the browser by simply double-clicking any highlighted text.

## Table of Contents

1. [Features](#features)  
2. [Installation](#installation)  
3. [Configuration](#configuration)  
4. [Usage](#usage)  
   - [Using Keys](#using-keys)  
   - [Using Phrases](#using-phrases)  
5. [Artisan Commands](#artisan-commands)  
   - [Add Existing Translations](#1-add-existing-translations)  
   - [Add Missing Translations (with Migration Option)](#2-add-missing-translations-with-migration-option)  
6. [Examples](#examples)  
7. [Publishing and Assets](#publishing-and-assets)

---

## Features

- **Database-driven localizations** – Store translations in the database instead of the traditional file-based approach.  
- **Built-in cache support** – Improve performance by caching translations.  
- **Browser-based editing** – Enable an edit mode that allows users to directly modify translations by double-clicking them.  
- **Laravel integration** – Seamless integration with Laravel’s localization features.  
- **Vue and Inertia integration** – Easily incorporate TransEdit with Vue or Inertia-based applications.  

---

## Installation

1. **Require the package via Composer:**

   ```bash
   composer require dialect/transedit
   ```

2. **Publish the configuration, assets, and migrations:**

   ```bash
   php artisan vendor:publish --provider="Dialect\TransEdit\TransEditServiceProvider"
   ```
   This command will publish:
   - Config file to `config/transedit.php`
   - Assets (JavaScript and Vue components) to your `resources` folder
   - Migrations to `database/migrations`

3. **Add the Vue component**  
   If you’re using Laravel Mix, add the TransEdit Vue component to your main JavaScript file (commonly `resources/assets/js/app.js` or `resources/js/app.js`) and recompile:

   ```js
   Vue.component('transedit', require('./components/transedit/TransEdit.vue'));
   ```

   Then, run:
   ```bash
   npm run dev
   ```
   or
   ```bash
   npm run prod
   ```
   according to your build process.

4. **Run the database migrations:**

   ```bash
   php artisan migrate
   ```

   This will create the necessary tables for storing translations.

5. **Enable TransEdit in your front-end**  
   - Include the TransEdit script in your layout (e.g., `resources/views/layout.blade.php`):

     ```html
     <script src="/js/transedit.js"></script>
     ```

   - If you are using Vue, register TransEdit as a global property in your `app.js` (or equivalent):

     ```js
     Vue.prototype.transEdit = window.transEdit;
     ```

   - If you are using **Inertia**, attach `transEdit` in your Inertia app setup, like so:

     ```js
     import { createInertiaApp } from '@inertiajs/inertia-vue3';
     import { createApp, h } from 'vue';

     createInertiaApp({
       title: (title) => `${title} - ${appName}`,
       resolve: (name) =>
         resolvePageComponent(
           `./Pages/${name}.vue`,
           import.meta.glob('./Pages/**/*.vue'),
         ),
       setup({ el, App, props, plugin }) {
         const theApp = createApp({ render: () => h(App, props) });

         // Make transEdit available globally
         theApp.config.globalProperties.transEdit = window.transEdit;

         return theApp
           .use(plugin)
           .mount(el);
       },
       progress: {
         color: '#4B5563',
       },
     });
     ```

That’s it! TransEdit is now installed and ready for use in your Laravel project.

---

## Configuration

After running the publish command, a configuration file will be placed at `config/transedit.php`. This file allows you to customize how TransEdit handles caching, database settings, default locale behavior, and more. Adjust these settings as desired to suit your application’s needs.

---

## Usage

### Using Keys

TransEdit offers convenient helper methods to set and retrieve translations using **keys**.

1. **Setting a translation key and value**  
   ```php
   transEdit()->setKey('greeting', 'Hello');
   ```

2. **Retrieving a translation by key**  
   ```php
   $value = transEdit()->getKey('greeting'); // returns "Hello"
   ```

3. **Helper function shorthand**
   ```php
   // Set a key
   transEdit('example.key', 'Some value', 'locale'); // 'locale' is optional

   // Get a key
   transEdit('example.key'); // returns "Some value"
   ```

4. **Working with variables**  
   ```php
   // Positional variables
   transEdit('You have $1 months left on your subscription of $2.', ['12', 'Netflix']);

   // Named variables
   transEdit('You have $MONTHS months left on your subscription of $SERVICE.', [
       'MONTHS' => '12',
       'SERVICE' => 'Netflix'
   ]);
   ```

5. **Enable/Disable edit mode**  
   ```php
   transEdit()->enableEditMode();  // Double-click on highlighted text to edit
   transEdit()->disableEditMode(); // Turn off in-browser editing
   ```
   > Note: Locale settings and edit-mode are session-based, meaning they apply to individual user sessions.

### Using Phrases

TransEdit also supports using **full phrases** or sentences directly in place of a key.

1. **Setting a phrase**  
   ```php
   // If the phrase does not exist in the database, TransEdit will create it
   transEdit('Welcome to our website!');

   // Optionally, you can include a locale
   transEdit('Welcome to our website!', null, 'en');
   ```

2. **Retrieving a phrase**  
   ```php
   echo transEdit('Welcome to our website!');
   ```

3. **Using variables in a phrase**  
   ```php
   transEdit('Hello, $NAME! Thank you for joining us.', ['NAME' => 'Alice']);
   ```

4. **Editing phrases in-browser**  
   ```php
   transEdit()->enableEditMode();
   ```
   Anywhere you have `transEdit('A new phrase here')` in your Blade view, you can double-click and edit it directly in the browser (if your Vue/JS setup is correct).

5. **Why use phrases?**  
   - **Speed & Convenience**: No need to manage separate key strings or maintain complex nested arrays.  
   - **Automatic Handling**: TransEdit manages insertion, updates, and caching for these phrases.  
   - **Iterative Development**: Wrap new text in `transEdit('...')`. Later, if you want more structure, you can easily rename or reorganize these translations.

---

## Artisan Commands

### 1. Add Existing Translations

This command scans your `lang` folder for Laravel language files and imports them into the TransEdit database:

```bash
php artisan transedit:add-lang-files-to-database
```

For example, if you have a file `lang/sv/article.php` containing:

```php
<?php

return [
  "recipe" => "Recept",
];
```

TransEdit will import the key `article.recipe` into the database. You can then retrieve it with:

```php
transEdit('article.recipe');
```

just like you would with `@lang('article.recipe')`.

### 2. Add Missing Translations (with Migration Option)

The **Add Missing Phrases** command scans your `resources/` folder for any calls to `transEdit('...')` that do not already exist in the database. It then either inserts them into your database **directly** or creates a **migration** so you can manage and track these new phrases in version control.  

```bash
php artisan transedit:add-missing-phrases
```

- **Default behavior**: Creates a new migration file in `database/migrations/`.  
  - After running the command, you’ll see a message indicating a migration has been created.  
  - You can then run `php artisan migrate` to insert the missing phrases into your database.  
  - This is ideal if you want to keep version control records of when new phrases are added.

- **Pushing directly to the database**:  
  ```bash
  php artisan transedit:add-missing-phrases --direct=1
  ```
  This immediately inserts any missing phrases for **all locales** into the database without creating a migration file.  

Below is a summary of the two approaches:

| Command                                     | Outcome                                                           |
|--------------------------------------------|-------------------------------------------------------------------|
| `php artisan transedit:add-missing-phrases`  | Creates a migration. Run `php artisan migrate` to insert phrases. |
| `php artisan transedit:add-missing-phrases --direct=1` | Directly inserts phrases into the database (no migration).        |

**Why choose a migration approach?**  
- Ensures that adding translations is tracked in source control.  
- Allows rollback with `php artisan migrate:rollback` if needed.  
- Useful in team environments where changes to translations should be approved or reviewed.  

**Why choose the direct approach?**  
- Faster and easier for quick adjustments.  
- Ideal for smaller projects or development environments where versioning of translations is not critical.

---

## Examples

Below is a short snippet demonstrating how to use TransEdit in your application:

```php
// Create or update a language name
transEdit()->setLocaleLanguageName('en', 'English');

// ----- Using Keys ----- //
// Set a key for the default or current locale
transEdit()->setKey('header.welcome', 'Welcome to our website');
transEdit('header.welcome', 'Welcome to our website'); // short syntax

// Retrieve a key
echo transEdit('header.welcome'); // "Welcome to our website"

// ----- Using Phrases ----- //
// Directly create or retrieve a phrase
echo transEdit('This is a phrase-based translation.');

// With variables
echo transEdit('Thank you, $NAME, for signing up!', ['NAME' => 'Alice']);

// Enable edit-mode (for the current user/session)
transEdit()->enableEditMode();
```

---

## Publishing and Assets

- **Assets**  
  Published to `resources/assets` (or the equivalent path for your setup).  
  They include Vue components and the JavaScript file `transedit.js`.

- **Config**  
  Published to `config/transedit.php`.

- **Migrations**  
  Published to `database/migrations`.

Make sure to run:

```bash
php artisan vendor:publish --provider="Dialect\TransEdit\TransEditServiceProvider"
```

to get all these files where they need to be.

---

**That’s everything you need to start translating and editing your Laravel app with TransEdit!**  

Feel free to open an issue or submit a pull request on [GitHub](https://github.com/forss-it/transedit) if you have any questions or improvements. Enjoy!