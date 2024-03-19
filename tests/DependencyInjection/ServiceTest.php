<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\DependencyInjection;

use Generator;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdater;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdaterInterface;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilder;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilderInterface;
use ITB\ShopwareBoolToYesNoUpdater\Language\AllLanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\Language\AllLanguagesIdAndNameFetcherInterface;
use ITB\ShopwareBoolToYesNoUpdater\Language\EntityTranslationsLanguagesIdAndNameFetcher;
use ITB\ShopwareBoolToYesNoUpdater\Language\EntityTranslationsLanguagesIdAndNameFetcherInterface;
use ITB\ShopwareBoolToYesNoUpdater\Tests\Kernel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ServiceTest extends TestCase
{
    public static function containerProvider(): Generator
    {
        $kernel = new Kernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        yield [$container];
    }

    #[DataProvider('containerProvider')]
    public function testGetBoolToYesNoUpdater(ContainerInterface $container): void
    {
        $boolToYesNoUpdater = $container->get(BoolToYesNoUpdater::class);
        self::assertInstanceOf(BoolToYesNoUpdater::class, $boolToYesNoUpdater);
    }

    #[DataProvider('containerProvider')]
    public function testGetBoolToYesNoUpdaterInterface(ContainerInterface $container): void
    {
        $boolToYesNoUpdater = $container->get(BoolToYesNoUpdaterInterface::class);
        self::assertInstanceOf(BoolToYesNoUpdater::class, $boolToYesNoUpdater);
    }

    #[DataProvider('containerProvider')]
    public function testGetAllLanguageIdsAndNameFetcher(ContainerInterface $container): void
    {
        $languageIdsAndNameFetcher = $container->get(AllLanguagesIdAndNameFetcher::class);
        self::assertInstanceOf(AllLanguagesIdAndNameFetcher::class, $languageIdsAndNameFetcher);
    }

    #[DataProvider('containerProvider')]
    public function testGetAllLanguagesIdAndNameFetcherInterface(ContainerInterface $container): void
    {
        $languageIdsAndNameFetcher = $container->get(AllLanguagesIdAndNameFetcherInterface::class);
        self::assertInstanceOf(AllLanguagesIdAndNameFetcher::class, $languageIdsAndNameFetcher);
    }

    #[DataProvider('containerProvider')]
    public function testGetEntityTranslationsLanguagesIdAndNameFetcher(ContainerInterface $container): void
    {
        $languageIdsAndNameFetcher = $container->get(EntityTranslationsLanguagesIdAndNameFetcher::class);
        self::assertInstanceOf(EntityTranslationsLanguagesIdAndNameFetcher::class, $languageIdsAndNameFetcher);
    }

    #[DataProvider('containerProvider')]
    public function testGetEntityTranslationsLanguagesIdAndNameFetcherInterface(ContainerInterface $container): void
    {
        $languageIdsAndNameFetcher = $container->get(EntityTranslationsLanguagesIdAndNameFetcherInterface::class);
        self::assertInstanceOf(EntityTranslationsLanguagesIdAndNameFetcher::class, $languageIdsAndNameFetcher);
    }

    #[DataProvider('containerProvider')]
    public function testGetYesNoTranslationCaseWhenThenBuilder(ContainerInterface $container): void
    {
        $yesNoTranslationCaseWhenThenBuilder = $container->get(YesNoTranslationCaseWhenThenBuilder::class);
        self::assertInstanceOf(YesNoTranslationCaseWhenThenBuilder::class, $yesNoTranslationCaseWhenThenBuilder);
    }

    #[DataProvider('containerProvider')]
    public function testGetYesNoTranslationCaseWhenThenBuilderInterface(ContainerInterface $container): void
    {
        $yesNoTranslationCaseWhenThenBuilder = $container->get(YesNoTranslationCaseWhenThenBuilderInterface::class);
        self::assertInstanceOf(YesNoTranslationCaseWhenThenBuilder::class, $yesNoTranslationCaseWhenThenBuilder);
    }
}
