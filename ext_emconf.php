<?php

$EM_CONF['redirect_manager'] = [
    'title' => 'Redirect Manager',
    'description' => 'Allows to manage URL- and page-redirects from a TYPO3 backend module.',
    'version' => '1.0.0',
    'category' => 'module',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-0.0.0',
            'typo3' => '9.0.0-10.9.9'
        ]
    ],
    'state' => 'stable',
    'author' => 'Dev-Team Typoheads',
    'author_email' => 'hello@typoheads.at',
    'author_company' => '',
    'clearcacheonload' => true
];
