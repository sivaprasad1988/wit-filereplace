<?php

declare(strict_types=1);

namespace Gingco\WitFilereplace;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Resolves the placeholder image's public URL, cache-busted by its own
 * mtime so replacing the shipped image doesn't get stuck behind browser
 * image caching.
 */
final class PlaceholderUrl
{
    private const RELATIVE_PATH = 'Resources/Public/Images/placeholder.svg';

    public static function get(): string
    {
        $url = PathUtility::getPublicResourceWebPath('EXT:wit_filereplace/' . self::RELATIVE_PATH);
        $absolutePath = ExtensionManagementUtility::extPath('wit_filereplace', self::RELATIVE_PATH);
        $mtime = @filemtime($absolutePath) ?: 0;

        return $url . '?v=' . $mtime;
    }
}
