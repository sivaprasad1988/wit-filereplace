<?php

declare(strict_types=1);

namespace Gingco\WitFilereplace\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Registers TypoScript, for every CType listed in the extension's
 * "excludedCTypes" setting, that marks its rendering as real-image (see
 * RealImageState) and wraps its output in markers so the middleware can
 * leave hardcoded <img> tags untouched. Runs after content-blocks' own
 * default TypoScript so our additions land on top of its `=<` copy instead
 * of being wiped out by it.
 */
#[AsEventListener(identifier: 'wit-filereplace/add-real-image-typoscript', after: 'content-blocks-typoscript')]
final class AddRealImageTypoScript
{
    public function __construct(private readonly ExtensionConfiguration $extensionConfiguration) {}

    public function __invoke(BootCompletedEvent $event): void
    {
        $cTypes = $this->getExcludedCTypes();
        if ($cTypes === []) {
            return;
        }

        $typoScript = '';
        foreach ($cTypes as $cType) {
            $typoScript .= <<<HEREDOC
tt_content.$cType {
    dataProcessing.9000 = Gingco\WitFilereplace\DataProcessing\ToggleRealImageProcessor
    stdWrap {
        postUserFunc = Gingco\WitFilereplace\DataProcessing\ToggleRealImageProcessor->popFlag
        wrap = <!--wit-filereplace:real-->|<!--/wit-filereplace:real-->
    }
}

HEREDOC;
        }

        ExtensionManagementUtility::addTypoScriptSetup($typoScript);
    }

    private function getExcludedCTypes(): array
    {
        $value = (string)($this->extensionConfiguration->get('wit_filereplace', 'excludedCTypes') ?? '');
        $cTypes = array_filter(array_map('trim', explode(',', $value)));

        return array_values(array_unique($cTypes));
    }
}
