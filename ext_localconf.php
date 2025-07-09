<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'IupacNomenclature',
        'Pi1',
        [
            \AyhanKoyun\IupacNomenclature\Controller\ReviewController::class => 'list, process'],
        [
            \AyhanKoyun\IupacNomenclature\Controller\ReviewController::class => 'process'
        ]
    );
});