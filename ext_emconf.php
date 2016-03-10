<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Sentry error reporting',
    'description' => 'Sentry integration with TYPO3. This extension sends TYPO3 errors and warning to the Sentry system for monitoring and analyzing.',
    'category' => 'services',
    'shy' => 0,
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => 0,
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'author' => 'move:elevator',
    'author_email' => 'typo3@move-elevator.de',
    'author_company' => '',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'version' => '1.1.0-dev',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'php' => '5.3.4-0.0.0',
                    'typo3' => '6.2.0-7.6.999',
                ),
            'conflicts' =>
                array(),
            'suggests' =>
                array(),
        ),
);
