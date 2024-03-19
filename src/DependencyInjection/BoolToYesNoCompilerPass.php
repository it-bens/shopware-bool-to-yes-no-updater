<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\DependencyInjection;

use Doctrine\DBAL\Connection;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdater;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdaterInterface;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilder;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilderInterface;
use ITB\ShopwareBoolToYesNoUpdater\Language\AllLanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\Language\AllLanguagesIdAndNameFetcherInterface;
use ITB\ShopwareBoolToYesNoUpdater\Language\EntityTranslationsLanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\Language\EntityTranslationsLanguagesIdAndNameFetcherInterface;
use ITB\SimpleWordsTranslator\TranslatorByName;
use ITB\SimpleWordsTranslator\TranslatorByNameInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class BoolToYesNoCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $translatorByNameDefinition = new Definition(TranslatorByName::class);
        $container->setDefinition(TranslatorByName::class, $translatorByNameDefinition);
        $container->setalias(TranslatorByNameInterface::class, TranslatorByName::class);

        $allLanguageIdAndNameFetcherDefinition = new Definition(AllLanguagesIdAndNameFetcher::class, [
            '$connection' => new Reference(Connection::class),
        ]);
        $container->setDefinition(AllLanguagesIdAndNameFetcher::class, $allLanguageIdAndNameFetcherDefinition);
        $container->setalias(AllLanguagesIdAndNameFetcherInterface::class, AllLanguagesIdAndNameFetcher::class);
        $entityTranslationsLanguageIdAndNameFetcherDefinition = new Definition(EntityTranslationsLanguagesIdAndNameFetcher::class, [
            '$connection' => new Reference(Connection::class),
        ]);
        $container->setDefinition(
            EntityTranslationsLanguagesIdAndNameFetcher::class,
            $entityTranslationsLanguageIdAndNameFetcherDefinition
        );
        $container->setalias(
            EntityTranslationsLanguagesIdAndNameFetcherInterface::class,
            EntityTranslationsLanguagesIdAndNameFetcher::class
        );

        $yesNoTranslationCaseWhenThenBuilderDefinition = new Definition(YesNoTranslationCaseWhenThenBuilder::class, [
            '$translator' => new Reference(TranslatorByNameInterface::class),
        ]);
        $container->setDefinition(YesNoTranslationCaseWhenThenBuilder::class, $yesNoTranslationCaseWhenThenBuilderDefinition);
        $container->setalias(YesNoTranslationCaseWhenThenBuilderInterface::class, YesNoTranslationCaseWhenThenBuilder::class);

        $boolToYesNoUpdaterDefinition = new Definition(BoolToYesNoUpdater::class, [
            '$connection' => new Reference(Connection::class),
            '$yesNoTranslationCaseWhenThenBuilder' => new Reference(YesNoTranslationCaseWhenThenBuilderInterface::class),
        ]);
        $container->setDefinition(BoolToYesNoUpdater::class, $boolToYesNoUpdaterDefinition);
        $container->setalias(BoolToYesNoUpdaterInterface::class, BoolToYesNoUpdater::class);
    }
}
