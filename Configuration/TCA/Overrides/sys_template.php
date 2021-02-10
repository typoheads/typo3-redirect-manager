<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Main extension template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'redirect_manager',
    'Configuration/TypoScript/',
    'Redirect Manager'
);
