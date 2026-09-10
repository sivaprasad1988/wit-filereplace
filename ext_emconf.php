<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'WIT File Replace',
    'description' => 'Replaces all frontend image sources with a placeholder image on Development instances',
    'category' => 'fe',
    'author' => 'Sivaprasad Sisupalan',
    'author_email' => 'siva@webofficeit.com',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
