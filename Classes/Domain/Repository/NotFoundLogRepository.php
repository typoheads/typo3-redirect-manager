<?php

namespace Typoheads\RedirectManager\Domain\Repository;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Typoheads\Utilities\Domain\Repository\Repository;

/**
 * Repository for managing "404 Not Found" log entries.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class NotFoundLogRepository extends Repository
{
    /**
     * @inheritDoc
     */
    public function initializeObject()
    {
        parent::initializeObject();

        // Get extension configuration
        /** @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $configuration = $extensionConfiguration->get('redirect_manager');

        if (!empty($configuration['notFoundLogStorage'])) {
            $this->getDefaultQuerySettings()->setStoragePageIds([(int)$configuration['notFoundLogStorage']]);
        } else {
            $this->getDefaultQuerySettings()->setStoragePageIds([0]);
        }
    }
}
