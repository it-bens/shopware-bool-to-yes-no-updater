<?php

declare(strict_types=1);

namespace DependencyInjection;

use Generator;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdater;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdaterInterface;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilder;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilderInterface;
use ITB\ShopwareBoolToYesNoUpdater\DependencyInjection\BoolToYesNoCompilerPass;
use ITB\ShopwareBoolToYesNoUpdater\Language\LanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\Language\LanguagesIdAndNameFetcherInterface;
use ITB\SimpleWordsTranslator\TranslatorByName;
use ITB\SimpleWordsTranslator\TranslatorByNameInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BoolToYesNoCompilerPassTest extends TestCase
{
    public static function processProvider(): Generator
    {
        $compilerPass = new BoolToYesNoCompilerPass();
        $container = new ContainerBuilder();

        yield 'compiler pass' => [$compilerPass, $container];
    }

    #[DataProvider('processProvider')]
    public function testProcess(BoolToYesNoCompilerPass $compilerPass, ContainerBuilder $container): void
    {
        $compilerPass->process($container);

        self::assertSame(TranslatorByName::class, $container->getDefinition(TranslatorByName::class)->getClass());
        self::assertSame(TranslatorByName::class, (string) $container->getAlias(TranslatorByNameInterface::class));
        self::assertSame(LanguagesIdAndNameFetcher::class, $container->getDefinition(LanguagesIdAndNameFetcher::class)->getClass());
        self::assertSame(LanguagesIdAndNameFetcher::class, (string) $container->getAlias(LanguagesIdAndNameFetcherInterface::class));
        self::assertSame(
            YesNoTranslationCaseWhenThenBuilder::class,
            $container->getDefinition(YesNoTranslationCaseWhenThenBuilder::class)->getClass()
        );
        self::assertSame(
            YesNoTranslationCaseWhenThenBuilder::class,
            (string) $container->getAlias(YesNoTranslationCaseWhenThenBuilderInterface::class)
        );
        self::assertSame(BoolToYesNoUpdater::class, $container->getDefinition(BoolToYesNoUpdater::class)->getClass());
        self::assertSame(BoolToYesNoUpdater::class, (string) $container->getAlias(BoolToYesNoUpdaterInterface::class));
    }
}
