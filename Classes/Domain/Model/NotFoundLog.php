<?php

namespace Typoheads\RedirectManager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Represents a "404 Not Found" log entry.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class NotFoundLog extends AbstractEntity
{
    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * Hashed URL.
     *
     * @var string
     */
    protected $hash;

    /**
     * URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Number of hits for this URL.
     *
     * @var int
     */
    protected $hitCount = 0;

    /**
     * Whether this URL has been resolved/fixed.
     *
     * @var bool
     */
    protected $isResolved = false;

    /**
     * Number of times this URL was hit after it was already resolved.
     *
     * @var int
     */
    protected $hasReappearedCount = 0;



    /**
     * Gets the creation time.
     *
     * @return \DateTime|null
     */
    public function getCrdate(): ?\DateTime
    {
        return $this->crdate;
    }



    /**
     * Sets the creation time.
     *
     * @param \DateTime $crdate New creation time
     *
     * @return void
     */
    public function setCrdate(\DateTime $crdate): void
    {
        $this->crdate = $crdate;
    }



    /**
     * Gets the hashes URL.
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }



    /**
     * Sets the hashed URL.
     *
     * @param string $hash New hashed URL
     *
     * @return void
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }



    /**
     * Gets the URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }



    /**
     * Sets the URL.
     *
     * @param string $url New URL
     *
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }



    /**
     * Gets number of hits for this URL.
     *
     * @return int
     */
    public function getHitCount(): int
    {
        return $this->hitCount;
    }



    /**
     * Sets number of hits for this URL.
     *
     * @param int $hitCount New number of hits
     *
     * @return void
     */
    public function setHitCount(int $hitCount): void
    {
        $this->hitCount = $hitCount;
    }



    /**
     * Gets whether this URL has been resolved/fixed.
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this->isResolved;
    }



    /**
     * Shortcut for Fluid.
     *
     * @return bool
     */
    public function getIsResolved(): bool
    {
        return $this->isResolved();
    }



    /**
     * Sets whether this URL has been resolved/fixed.
     *
     * @param bool $isResolved New state
     *
     * @return void
     */
    public function setIsResolved(bool $isResolved): void
    {
        $this->isResolved = $isResolved;
    }



    /**
     * Gets the number of times this URL was hit after it was already resolved.
     *
     * @return int
     */
    public function getHasReappearedCount(): int
    {
        return $this->hasReappearedCount;
    }



    /**
     * Sets the number of times this URL was hit after it was already resolved.
     *
     * @param int $hasReappearedCount New count
     *
     * @return void
     */
    public function setHasReappearedCount(int $hasReappearedCount): void
    {
        $this->hasReappearedCount = $hasReappearedCount;
    }



    /**
     * Gets whether this URL was hit again after it was already resolved/fixed.
     *
     * @return bool True when the URL was hit again after being resolved, otherwise false
     */
    public function hasReappeared(): bool
    {
        return $this->getHasReappearedCount() > 0;
    }



    /**
     * Shortcut for Fluid.
     *
     * @return bool True when the URL was hit again after being resolved, otherwise false
     */
    public function getHasReappeared(): bool
    {
        return $this->hasReappeared();
    }



    /**
     * Gets a status identifier describing, what the current state of the log entry is.
     *
     * @return string Status identifier
     */
    public function getStatus(): string
    {
        if ($this->isResolved() && !$this->hasReappeared()) {
            return 'isResolved';
        }

        if ($this->hasReappeared()) {
            return 'isResolvedButHasReappeared';
        }

        if ($this->getHitCount() === 1) {
            return 'isNew';
        }

        return 'hasNewHits';
    }
}
