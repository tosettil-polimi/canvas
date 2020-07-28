# Upgrade Guide

## Table of Contents

- [Upgrading to 5.4.0 from 5.3](#upgrading-to-540-from-53)
- [Upgrading to 5.3.0 from 5.2](#upgrading-to-530-from-52)
- [Upgrading to 5.2.0 from 5.1](#upgrading-to-520-from-51)
- [Upgrading to 5.1.0 from 5.0](#upgrading-to-510-from-50)

## Upgrading to 5.4.0 from 5.3

> Note: Exporting and re-importing your data allowed the migrations to be consolidated into one file as well as
> removing the requirement of the `doctrine/dbal` dependency.

### Export your data

> Important: The process for migrating data will be unique based on your choice of IDE and database.  

The first step is to export all data in Canvas-related tables to a SQL dump. *The key here is to not allow the
 `DROP TABLE` or `CREATE TABLE` syntax into the export.* The one thing that you do need from this dump are the
 `INSERT` statements. The following tables need to be included in the export:  

- `canvas_posts`
- `canvas_posts_tags`
- `canvas_posts_topics`
- `canvas_tags`
- `canvas_topics`
- `canvas_user_meta`
- `canvas_views`
- `canvas_visits`

Once completed, you can drop those tables from your database.

> Optional: If you want to keep the `migrations` table as minimal as possible, you may delete all references to
> `_canvas` records.

### Updating dependencies

Update your `cnvs/canvas` dependency to `^5.4` in your `composer.json` file. Upgrade the package to the latest version:

```bash
composer update
```

### Migrations

Run the new migrations using the `migrate` Artisan command:

```bash
php artisan migrate
```

### Import your data

You may now import the SQL dump that you created above into your database. Remember, your database and IDE will
 determine if you should run into any errors while performing this action. 
 
For instance, I use [Sequel Pro](http://sequelpro.com). When I exported my data, I made sure to un-check the
 `DROP TABLE` syntax. When I ran the import, I had to ensure the **On SQL Error** was set to *Ignore and continue* since
 the dump still contained `CREATE TABLE` statements. Sequel Pro will throw errors since those tables already exist,
 however they can be ignored since the import still inserts the data properly. 

### Configuration

The base path variable name in `config/canvas.php` changed to be consistent with the newly-added domain variable. 
You'll need to make sure your `.env` file is up to date with the correct variable: `CANVAS_PATH`.
 
### Assets

Re-publish the assets using the `canvas:publish` Artisan command:

```bash
php artisan canvas:publish
```

Clear any cached views using the `view:clear` Artisan command:

```bash
php artisan view:clear
```

## Upgrading to 5.3.0 from 5.2

> **Note:** The `5.3.0` minor update contains does not contain breaking changes.

### Updating dependencies

Update your `cnvs/canvas` dependency to `^5.3` in your `composer.json` file. Upgrade the package to the latest version:

```bash
composer update
```

### Assets

Re-publish the assets using the `canvas:publish` Artisan command:

```bash
php artisan canvas:publish
```

Clear any cached views using the `view:clear` Artisan command:

```bash
php artisan view:clear
```

## Upgrading to 5.2.0 from 5.1

> **Important:** The `Canvas\Http\Middleware\ViewThrottle` middleware was renamed to `Canvas\Http\Middleware
>\Session`. Update any usages of this class.

> **Important:** The `meta` field for posts will now only support a title, description, and canonical link. The
> `og_*` and `twitter_*` tags were unnecessarily specific, so they were deprecated. If you use those tags in your
> frontend templates, simply update them with the new title and description fields.

### Updating dependencies

Update your `cnvs/canvas` dependency to `^5.2` in your `composer.json` file. Upgrade the package to the latest version:

```bash
composer update
```

### Migrations

Run the new migrations using the `migrate` Artisan command:

```bash
php artisan migrate
```

### Assets

Re-publish the assets using the `canvas:publish` Artisan command:

```bash
php artisan canvas:publish
```

Clear any cached views using the `view:clear` Artisan command:

```bash
php artisan view:clear
```

## Upgrading to 5.1.0 from 5.0

### Updating dependencies

Update your `cnvs/canvas` dependency to `^5.1` in your `composer.json` file. Upgrade the package to the latest version:

```bash
composer update
```

### Configuration
 
Add the following line to the Storage block in your `config/canvas.php` file:

```php
'upload_filesize' => env('CANVAS_UPLOAD_FILESIZE', 3145728),
```

Rename the Weekly Digest configuration variable:

> Note: Make sure that you update your `.env` file as well to reflect this variable change

```php
'mail' => [
    'enabled' => env('CANVAS_MAIL_ENABLED', false),
],
```

### Service Provider

Update the `boot()` method in your `app/Providers/CanvasServiceProvider.php` file:

```php
$this->app->booted(function () {
    $schedule = resolve(Schedule::class);
    $schedule->command('canvas:digest')
        ->weekly()
        ->mondays()
        ->timezone(config('app.timezone'))
        ->at('08:00')
        ->when(function () {
            return config('canvas.mail.enabled');
        });
});
```

### Migrations

Run the new migrations using the `migrate` Artisan command:

```bash
php artisan migrate
```

### Assets

Re-publish the assets using the `canvas:publish` Artisan command:

```bash
php artisan canvas:publish
```

Clear any cached views using the `view:clear` Artisan command:

```bash
php artisan view:clear
```
