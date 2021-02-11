<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


$GLOBALS['TCA']['sys_redirect']['columns']['source_path']['config']['type'] = 'prefillSourcePathFromRedirectManager';
