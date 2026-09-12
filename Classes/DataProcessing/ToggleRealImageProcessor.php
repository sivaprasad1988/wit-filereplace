<?php

declare(strict_types=1);

namespace Woit\WitFilereplace\DataProcessing;

use Woit\WitFilereplace\RealImageState;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Marks a content element as real-image while it renders, via TypoScript
 * wired into tt_content.<CType>.dataProcessing (push) and
 * tt_content.<CType>.stdWrap.postUserFunc (pop) for excluded CTypes.
 */
final class ToggleRealImageProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        RealImageState::push();

        return $processedData;
    }

    public function popFlag(string $content, array $conf): string
    {
        RealImageState::pop();

        return $content;
    }
}
