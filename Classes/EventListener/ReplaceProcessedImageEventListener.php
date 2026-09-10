<?php

declare(strict_types=1);

namespace Gingco\WitFilereplace\EventListener;

use Gingco\WitFilereplace\PlaceholderUrl;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;

/**
 * Redirects every FAL-processed image (content elements, <f:image>, etc.) to a
 * static placeholder while running on a Development instance.
 */
#[AsEventListener('wit-filereplace/replace-processed-image')]
final class ReplaceProcessedImageEventListener
{
    public function __invoke(AfterFileProcessingEvent $event): void
    {
        if (!Environment::getContext()->isDevelopment()) {
            return;
        }

        $event->getProcessedFile()->updateProcessingUrl(PlaceholderUrl::get());
    }
}
