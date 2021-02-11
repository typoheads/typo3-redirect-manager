<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1613041067] = [
    'nodeName' => 'prefillSourcePathFromRedirectManager',
    'priority' => 40,
    'class' => \Typoheads\RedirectManager\Tca\PrefillSourcePathFromRedirectManagerElement::class
];
