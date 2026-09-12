<?php

use Woit\WitFilereplace\Middleware\ReplaceImageSourceMiddleware;

return [
    'frontend' => [
        'woit/filereplace/replace-image-source' => [
            'target' => ReplaceImageSourceMiddleware::class,
            'before' => [
                'typo3/cms-frontend/eid',
            ],
        ],
    ],
];
