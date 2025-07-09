<?php

$EM_CONF['iupacnomenclature'] = [
    'title' => 'Iupac Nomenclature',
    'description' => 'An extension to find the right IUPAC nomenclature for a given chemical structure.',
    'category' => 'frontend',
    'author' => 'Ayhan Koyun',
    'author_email' => 'ayhankoyun@hotmail.de',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.99.99',
            'php' => '8.0.0-8.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
