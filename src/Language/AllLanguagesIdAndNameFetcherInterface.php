<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Language;

interface AllLanguagesIdAndNameFetcherInterface
{
    /**
     * @return list<array{id: string, name: string}>
     */
    public function fetchLanguagesIdAndName(): array;
}
