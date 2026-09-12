<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Placeholder Image Guard',
    'description' => 'Replaces frontend image sources with a placeholder image on Development instances, with settings to restrict this to the frontend, exclude specific content element CTypes, and use a custom placeholder image.',
    'category' => 'fe',
    'author' => 'Sivaprasad Sisupalan',
    'author_email' => 'siva@webofficeit.com',
    'state' => 'stable',
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
