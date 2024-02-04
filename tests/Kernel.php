<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests;

use Doctrine\DBAL\Connection;
use ITB\ShopwareBoolToYesNoUpdater\DependencyInjection\BoolToYesNoCompilerPass;
use ITB\ShopwareBoolToYesNoUpdater\Tests\Mock\DbalConnection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->addCompilerPass(new BoolToYesNoCompilerPass());

            // All services are made public to use them via container.
            $container->addCompilerPass(new PublicForTestsCompilerPass());

            // Replace doctrine connection with a mock.
            $mockConnectionDefinition = new Definition(DbalConnection::class);
            $container->setDefinition(Connection::class, $mockConnectionDefinition);
        });
    }

    public function registerBundles(): iterable
    {
        return [];
    }
}
