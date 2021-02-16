<?php

namespace Typoheads\RedirectManager\Domain\Repository;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Typoheads\Utilities\Domain\Repository\Repository;
use Typoheads\RedirectManager\Domain\Repository\Demand;

/**
 * Repository for managing "404 Not Found" log entries.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class NotFoundLogRepository extends Repository
{

    /**
     * @var Demand
     */
    protected $demand;


    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct($objectManager);
        $this->demand = new Demand();
    }



    /**
     * @param \Typoheads\RedirectManager\Domain\Repository\Demand $demand
     */
    public function setDemand(Demand $demand)
    {
        $this->demand = $demand;
    }

    /**
     * Used within the backend module, which also includes the hidden records, but never deleted records.
     *
     * @return array
     */
    public function findByDemand(): array
    {
        $query =  $this->getQueryForDemand()
            ->setLimit($this->demand->getLimit())
            ->setOffset($this->demand->getOffset());

        return $query
            ->execute()
            ->toArray();
    }

    /**
     * @return int
     */
    public function countRedirectsByByDemand(): int
    {
        return $this->getQueryForDemand()->execute()->count();
    }

    /**
     * Prepares the QueryBuilder with Constraints from the Demand
     *
     * @return Query
     */
    protected function getQueryForDemand(): Query
    {
        $query = $this->createQuery();

        $constraints = [];
        if ($this->demand->hasUrl()) {
            $constraints[] = $query->like('url', '%' . $this->demand->getUrl() . '%');
        }
        if ($this->demand->hasStatus()) {
            switch($this->demand->getStatus()) {
                case 'resolved':
                    $constraints[] = $query->logicalAnd([
                        $query->equals('is_resolved', 1),
                        $query->equals('has_reappeared_count', 0)
                    ]);
                    break;
                case 'reappeared':
                    $constraints[] = $query->greaterThan('has_reappeared_count', 0);
                    break;
                case 'new':
                    $constraints[] = $query->equals('hit_count', 1);
                    break;
                case 'new-hits':
                    $constraints[] = $query->logicalAnd([
                        $query->greaterThan('hit_count', 1),
                        $query->equals('has_reappeared_count', 0)
                    ]);

                    break;
            }
            $constraints[] = $query->like('url', '%' . $this->demand->getUrl() . '%');
        }
        if ($this->demand->hasDateFrom()) {
            $constraints[] = $query->greaterThanOrEqual('crdate', strtotime($this->demand->getDateFrom()));
        }
        if ($this->demand->hasDateTo()) {
            $constraints[] = $query->lessThanOrEqual('crdate', strtotime($this->demand->getDateTo()));
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        if($_POST['sorting']) {
            switch($_POST['sorting']) {
                case 'date-asc':
                    $query->setOrderings([
                        'crdate' => QueryInterface::ORDER_ASCENDING,
                        'hit_count' => QueryInterface::ORDER_DESCENDING
                    ]);
                    break;
                case 'date-desc':
                    $query->setOrderings([
                        'crdate' => QueryInterface::ORDER_DESCENDING,
                        'hit_count' => QueryInterface::ORDER_DESCENDING
                    ]);
                    break;
            }
        }

        return $query;
    }

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

        $this->setDefaultOrderings([
            'hit_count' => QueryInterface::ORDER_DESCENDING,
            'crdate' => QueryInterface::ORDER_DESCENDING
        ]);
    }
}
