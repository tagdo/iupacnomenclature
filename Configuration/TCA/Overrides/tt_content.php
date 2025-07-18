<?php

defined('TYPO3') or die();

call_user_func(static function () {
    $extKey = 'iupacnomenclature';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/',
        'Iupac nomenclature'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $extKey,
        'Pi1',
        'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_be.xlf:plugin_pi1.title'
    );

    $pluginSignature = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($extKey) . '_pi1');
    
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';
});