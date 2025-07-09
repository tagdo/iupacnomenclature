<<<<<<< HEAD
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
=======
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
>>>>>>> 0b9d07b48938ab5ee9a4bd675d164dc503c35af5
});