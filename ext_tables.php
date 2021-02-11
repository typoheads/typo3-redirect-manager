<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Register backend module for managing Solr
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Typoheads.RedirectManager',
    'site',
    'Redirects',
    'after:redirects',
    [
        \Typoheads\RedirectManager\Controller\Backend\Module\RedirectsController::class => 'index,listNotFound,deleteNotFoundLog,resolveNotFoundLog,unresolveNotFoundLog'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:redirect_manager/Resources/Public/Icons/Backend/Module/redirects.svg',
        'labels' => 'LLL:EXT:redirect_manager/Resources/Private/Language/locallang_module_redirects.xlf'
    ]
);
