<?php

declare(strict_types=1);

namespace Woit\WitFilereplace;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Resolves the placeholder image's public URL, cache-busted by its own
 * mtime so replacing the shipped image doesn't get stuck behind browser
 * image caching. Uses the "placeholderImage" extension setting when
 * configured (path or EXT: reference), otherwise the shipped default.
 */
final class PlaceholderUrl
{
    private const RELATIVE_PATH = 'Resources/Public/Images/placeholder.svg';

    public static function get(): string
    {
        $absolutePath = self::getConfiguredAbsolutePath();
        $url = $absolutePath !== null
            ? PathUtility::getAbsoluteWebPath($absolutePath)
            : PathUtility::getPublicResourceWebPath('EXT:wit_filereplace/' . self::RELATIVE_PATH);

        $absolutePath ??= ExtensionManagementUtility::extPath('wit_filereplace', self::RELATIVE_PATH);
        $mtime = @filemtime($absolutePath) ?: 0;

        return $url . '?v=' . $mtime;
    }

    private static function getConfiguredAbsolutePath(): ?string
    {
        $configuredPath = trim((string)(
            GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('wit_filereplace', 'placeholderImage') ?? ''
        ));
        if ($configuredPath === '') {
            return null;
        }

        $absolutePath = GeneralUtility::getFileAbsFileName($configuredPath);

        return $absolutePath !== '' && @file_exists($absolutePath) ? $absolutePath : null;
    }
}
