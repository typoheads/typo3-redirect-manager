<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'tx_redirectmanager_not_found_log.listTitle',
        'label' => 'url',
        'default_sortby' => 'crdate DESC',
        'crdate' => 'crdate',
        'searchFields' => 'url,hash,hit_count,is_resolved,has_reappeared_count',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/svgs/actions/actions-link.svg'
    ],
    'types' => [
        '1' => [
            'showitem' => '
                url,hash,hit_count,is_resolved,has_reappeared_count
            '
        ]
    ],
    'columns' => [
        'crdate' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'url' => [
            'label' => 'LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.url',
            'config' => [
                'type' => 'input',
                'renderType' => '',
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],
        'hash' => [
            'label' => 'LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.hash',
            'config' => [
                'type' => 'input',
                'renderType' => '',
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],
        'hit_count' => [
            'label' => 'LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.hit_count',
            'config' => [
                'type' => 'input',
                'renderType' => '',
                'eval' => 'int,trim,required',
                'default' => 0
            ]
        ],
        'is_resolved' => [
            'label' => 'LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.is_resolved',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.is_resolved.1', 1]
                ],
                'default' => 0
            ]
        ],
        'has_reappeared_count' => [
            'label' => 'LLL:EXT:redirect_manager/Resources/Private/Language/Backend/Backend.xlf:tx_redirectmanager_not_found_log.has_reappeared_count',
            'config' => [
                'type' => 'input',
                'renderType' => '',
                'eval' => 'int,trim,required',
                'default' => 0
            ]
        ]
    ]
];
