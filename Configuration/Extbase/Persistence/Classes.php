<?php

declare(strict_types=1);

return [
    \Typoheads\RedirectManager\Domain\Model\NotFoundLog::class => [
        'tableName' => 'tx_redirectmanager_not_found_log',
        'properties' => [
            'crdate' => [
                'fieldName' => 'crdate'
            ]
        ]
    ]
];
