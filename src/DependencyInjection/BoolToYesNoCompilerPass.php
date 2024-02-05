<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\DependencyInjection;

use Doctrine\DBAL\Connection;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdater;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdaterInterface;
use ITB\ShopwareBoolToYesNoUpdater\LanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\LanguagesIdAndNameFetcherInterface;
use ITB\ShopwareBoolToYesNoUpdater\YesNoTranslationCaseWhenThenBuilder;
use ITB\ShopwareBoolToYesNoUpdater\YesNoTranslationCaseWhenThenBuilderInterface;
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

        $languageIdAndNameFetcherDefinition = new Definition(LanguagesIdAndNameFetcher::class, [
            '$connection' => new Reference(Connection::class),
        ]);
        $container->setDefinition(LanguagesIdAndNameFetcher::class, $languageIdAndNameFetcherDefinition);
        $container->setalias(LanguagesIdAndNameFetcherInterface::class, LanguagesIdAndNameFetcher::class);

        $yesNoTranslationCaseWhenThenBuilderDefinition = new Definition(YesNoTranslationCaseWhenThenBuilder::class, [
            '$languagesIdAndNameFetcher' => new Reference(LanguagesIdAndNameFetcherInterface::class),
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