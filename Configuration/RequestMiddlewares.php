<?php

use Gingco\WitFilereplace\Middleware\ReplaceImageSourceMiddleware;

return [
    'frontend' => [
        'wit/filereplace/replace-image-source' => [
            'target' => ReplaceImageSourceMiddleware::class,
            'before' => [
                'typo3/cms-frontend/eid',
            ],
        ],
    ],
];
