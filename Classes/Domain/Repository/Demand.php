<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Typoheads\RedirectManager\Domain\Repository;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Demand Object for filtering redirects in the backend module
 */
class Demand
{

    /**
     * @var string
     */
    protected $url;

    /**
     *
     * resolved|reappeared|new|new-hits
     *
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $dateFrom;

    /**
     * @var string
     */
    protected $dateTo;

    /**
     * @var int
     */
    protected $limit = 50;

    /**
     * @var int
     */
    protected $page;

    /**
     * Demand constructor.
     * @param int $page
     * @param string $url
     * @param string $status
     * @param string $dateFrom
     * @param string $dateTo
     */
    public function __construct(int $page = 1, string $url = '', string $status = '', string $dateFrom = '', string $dateTo = '')
    {
        $this->page = $page;
        $this->url = $url;
        $this->status = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * Creates a Demand object from the current request.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @return Demand
     */
    public static function createFromRequest(Request $request): Demand
    {

        $arguments = $request->getArguments();

        /** @TODO Read arguments directly from get and post for now. Figure out how to access arguments in BE context */
        $arguments = array_merge($_GET, $_POST);

        $page = (int)($arguments['page'] ?? 1);
        $demand = $arguments['demand'] ?? false;

        if (empty($demand)) {
            return new self($page);
        }
        $url = $demand['url'] ?? '';
        $status = $demand['status'] ?? '';
        $dateFrom = $demand['dateFrom'] ?? '';
        $dateTo = $demand['dateTo'] ?? '';

        return new self($page, $url, $status, $dateFrom, $dateTo);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function hasUrl(): bool
    {
        return $this->url !== '';
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasStatus(): bool
    {
        return $this->status !== '';
    }

    /**
     * @return string
     */
    public function getDateFrom(): string
    {
        return $this->dateFrom;
    }

    /**
     * @return bool
     */
    public function hasDateFrom(): bool
    {
        return $this->dateFrom !== '';
    }

    /**
     * @return string
     */
    public function getDateTo(): string
    {
        return $this->dateTo;
    }

    /**
     * @return bool
     */
    public function hasDateTo(): bool
    {
        return $this->dateTo !== '';
    }

    /**
     * @return bool
     */
    public function hasConstraints(): bool
    {
        return $this->hasStatus()
            || $this->hasUrl()
            || $this->hasDateFrom()
            || $this->hasDateTo();
    }

    /**
     * The current Page of the paginated redirects
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Offset for the current set of records
     *
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        $parameters = [];
        if ($this->hasUrl()) {
            $parameters['url'] = $this->getUrl();
        }
        if ($this->hasStatus()) {
            $parameters['status'] = $this->getStatus();
        }
        if ($this->hasDateFrom()) {
            $parameters['dateFrom'] = $this->getDateFrom();
        }
        if ($this->hasDateTo()) {
            $parameters['dateTo'] = $this->getDateTo();
        }
        return $parameters;
    }
}
