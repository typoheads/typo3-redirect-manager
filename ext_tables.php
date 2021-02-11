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

// Add context sensitive help (CSH) to the backend module
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '_MOD_site_redirectmanager_listnotfound',
    'EXT:redirect_manager/Resources/Private/Language/locallang_csh_redirectmanager_listnotfound.xlf'
);
