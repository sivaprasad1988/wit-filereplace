<?php

declare(strict_types=1);

namespace Gingco\WitFilereplace\EventListener;

use Gingco\WitFilereplace\PlaceholderUrl;
use Gingco\WitFilereplace\RealImageState;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;

/**
 * Redirects every FAL-processed image (content elements, <f:image>, etc.) to a
 * static placeholder while running on a Development instance, unless the
 * rendering content element's CType is on the excluded-CType allowlist (see
 * RealImageState). When the "restrictToFrontend" setting is enabled (default),
 * backend previews (e.g. page module) are left untouched.
 */
#[AsEventListener('wit-filereplace/replace-processed-image')]
final class ReplaceProcessedImageEventListener
{
    public function __construct(private readonly ExtensionConfiguration $extensionConfiguration) {}

    public function __invoke(AfterFileProcessingEvent $event): void
    {
        if (!Environment::getContext()->isDevelopment()) {
            return;
        }

        if ($this->restrictToFrontend() && !$this->isFrontendRequest()) {
            return;
        }

        if (RealImageState::isActive()) {
            return;
        }

        $event->getProcessedFile()->updateProcessingUrl(PlaceholderUrl::get());
    }

    private function restrictToFrontend(): bool
    {
        return (bool)($this->extensionConfiguration->get('wit_filereplace', 'restrictToFrontend') ?? true);
    }

    private function isFrontendRequest(): bool
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        return $request instanceof ServerRequestInterface && ApplicationType::fromRequest($request)->isFrontend();
    }
}
