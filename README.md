# Shopware Bool To Yes/No Updater

A lot of Shopware entities support translations. The translated fields are stored inside entity translation tables.

This package provides the ability to transform boolean entity fields into "translated booleans" like "Yes" and "No". These translated fields can come in handy if a boolean field should shown in a storefront.

## Is this a Shopware Plugin?

No. While this package is tied to Shopware, it won't react on any events on it's own. It is meant to be used by a Shopware plugin.

## How can add this package in a Shopware plugin?

First, the package has to be installed via composer:

```bash
composer require it-bens/shopware-bool-to-yes-no-updater
```

This can be done in the plugin's `composer.json` file. But don't forget to enable composer in the plugin class.

```php
public function executeComposerCommands(): bool
{
    return true;
}
```

This package provides a Symfony compiler pass that will add all the services to the Shopware plugin service container. It can be added to the plugin's `build` method.

```php
use ITB\ShopwareBoolToYesNoUpdater\DependencyInjection\BoolToYesNoCompilerPass;

public function build(ContainerBuilder $container): void
{
    // ...
    parent::build($container);

    $container->addCompilerPass(new BoolToYesNoCompilerPass());
}
```

## How can I use this package?

The actual work is done by the `BoolToYesNoUpdater` service. It should be injected into the desired service via the `BoolToYesNoUpdaterInterface`. The `update` method will create an SQL update query that will writes the boolean-translations into given translation table. The boolean value is taken from en entity table and a defined list of fields. The method also requires a list of languages which should be considered and a list of entity IDs that should be updated.

The usage could look like this:

```php
/** @var BoolToYesNoUpdaterInterface $boolToYesNoUpdater */
/** @var list<array{id: string, name: string}> $languages */
/** @var list<string> $fields */
/** @var list<string> $ids */

$boolToYesNoUpdater->update(
    $languages,
    $defaultLanguage,
    'entity_table',
    'entity_translation_table',
    ['field_1', 'field_2'],
    $ids
);
```

The language information looks a little strange but it can be fetched with services from this package. The `AllLanguagesIdAndNameFetcher` just returns a list of all language IDs and names, that are installed in Shopware. The `EntityTranslationsLanguagesIdAndNameFetcher` returns a list of language IDs and names based on the actual existent entity translations.

```php
/** @var AllLanguagesIdAndNameFetcherInterface $allLanguagesIdAndNameFetcher */
$allLanguagesIdAndNameFetcher->fetch();
```
    
```php
/** @var EntityTranslationsLanguagesIdAndNameFetcherInterface $entityTranslationsLanguagesIdAndNameFetcher */
/** @var string $entityTable */
/** @var string $entityTranslationTable */
/** @var list<string> $entityIds */

$entityTranslationsLanguagesIdAndNameFetcher->fetch(
    $entityTable,
    $entityTranslationTable,
    $entityIds
);
```

## Default language and fallback translations

This package uses the `it-bens/simple-words-translator`. It provides translations for some languages but of cause not for all languages in the world ... or that are known to Shopware. That's why a default language can be defined. If a translation is not found for a language by it's name, the default language will be used instead.

Furthermore, a default value can be defined for all table entries that should be updated because the entity id was passed, but contains not valid boolean value for the given field. The most convenient default value is `null`.

## Performance and Indexer loops

The `BoolToYesNoUpdater` service is designed to be used in a [Shopware data indexer](https://developer.shopware.com/docs/guides/plugins/plugins/framework/data-handling/add-data-indexer.html). To improve the performance and to prevent loop calls by triggering the DAL, the updater uses only plain SQL code and no higher abstractions. However, the queries are executed on a doctrine connection, so the queries should still be safe.

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive) and every pull request that provides other or even better code to this package.

You are all breathtaking!