<?php

namespace Typoheads\RedirectManager\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Controller\ErrorPageController;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Typoheads\Utilities\Utility\ConnectionPoolTrait;

/**
 * Provides custom error-handlers.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class PageErrorHandling implements PageErrorHandlerInterface
{
    use ConnectionPoolTrait;

    /**
     * @inheritDoc
     */
    public function handlePageError(ServerRequestInterface $request, string $message, array $reasons = []): ResponseInterface
    {
        // Get extension configuration
        /** @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $configuration = $extensionConfiguration->get('redirect_manager');

        // If no further configuration is present, simply return the default 404 response
        if (empty($configuration)) {
            return $this->getResponse($request, $message, $reasons, 'default');
        }

        // Log this 404 error request
        if (!empty($configuration['enableNotFoundLogging'])) {
            $this->addLogEntry($request, $message, $reasons);
        }

        // Return final response
        return $this->getResponse(
            $request,
            $message,
            $reasons,
            $configuration['pageErrorHandlingResponseType'],
            $configuration['pageErrorHandlingResponseValue']
        );
    }



    /**
     * Gets the final 404 response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     * @param string $type Type of response to get
     * @param string $value (Optional) Value for the response (default to nothing)
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     */
    private function getResponse(ServerRequestInterface $request, string $message, array $reasons, string $type, string $value = ''): ResponseInterface
    {
        switch ($type) {
            case 'url':
                return $this->getRedirectToUrlResponse($request, $message, $reasons, $value);

            case 'page':
                return $this->getRedirectToPageResponse($request, $message, $reasons, (int)$value);

            case 'class':
                return $this->getErrorHandlingClassResponse($request, $message, $reasons, $value);

            case 'exception':
                throw new PageNotFoundException();

            default:
                return $this->getDefaultResponse($request, $message, $reasons);
        }
    }



    /**
     * Gets a default 404 error response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getDefaultResponse(ServerRequestInterface $request, string $message, array $reasons = []): ResponseInterface
    {
        $content = GeneralUtility::makeInstance(ErrorPageController::class)->errorAction(
            'Page Not Found',
            'The page did not exist or was inaccessible.' . ($message ? ' Reason: ' . $message : '')
        );
        return new HtmlResponse($content, 404);
    }



    /**
     * Gets a response to redirect to a URL.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     * @param string $url URL to redirect to
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getRedirectToUrlResponse(ServerRequestInterface $request, string $message, array $reasons = [], string $url): ResponseInterface
    {
        return new RedirectResponse($url, 404);
    }



    /**
     * Gets a response to redirect to a TYPO3 page.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     * @param int $pageUid UID of the page to redirect to
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getRedirectToPageResponse(ServerRequestInterface $request, string $message, array $reasons = [], int $pageUid): ResponseInterface
    {
        /** @var \TYPO3\CMS\Core\Site\SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

        try {
            $site = $siteFinder->getSiteByPageId($pageUid);
        } catch (SiteNotFoundException $e) {
            // Return default response, if not site configuration was found for the given page UID
            return $this->getDefaultResponse($request, $message, $reasons);
        }

        $uri = $site->getRouter()->generateUri($pageUid);
        $url = (string)$uri;

        return new RedirectResponse($url, 404);
    }



    /**
     * Gets a response from another ErrorHandling-class.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     * @param string $class FQDN of the class to instantiate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getErrorHandlingClassResponse(ServerRequestInterface $request, string $message, array $reasons = [], string $class): ResponseInterface
    {
        // Return default response, if the given class does not exist
        if (!class_exists($class)) {
            return $this->getDefaultResponse($request, $message, $reasons);
        }

        /** @var \TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerInterface $errorHandler */
        $errorHandler = GeneralUtility::makeInstance($class);

        // Return default response, if the given error handler is invalid
        if (!($errorHandler instanceof PageErrorHandlerInterface)) {
            return $this->getDefaultResponse($request, $message, $reasons);
        }

        return $errorHandler->handlePageError($request, $message, $reasons);
    }



    /**
     * Adds a log entry for the given 404 request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request
     * @param string $message Error message
     * @param array $reasons Error reasons
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function addLogEntry(ServerRequestInterface $request, string $message, array $reasons = []): void
    {
        $now = new \DateTime();

        // Create unique hash from the URL
        $url = (string)$request->getUri();
        $hash = sha1($url);

        // Create data to be written into the database
        $entry = [
            'crdate' => $now->getTimestamp(),
            'deleted' => 0,
            'hash' => $hash,
            'url' => $url,
            'hit_count' => 0,
            'is_resolved' => 0,
            'has_reappeared_count' => 0
        ];

        // Check if there already is a log entry for this specific URL
        $query = $this->getConnectionPool()->getQueryBuilderForTable('tx_redirectmanager_not_found_log');

        $previousEntry = $query->select('*')
            ->from('tx_redirectmanager_not_found_log')
            ->where(
                $query->expr()->eq('hash', $query->createNamedParameter($hash))
            )
            ->execute()
            ->fetch();

        // There is no log entry for this URL yet; create a new one
        if (empty($previousEntry)) {
            $query->resetQueryParts();
            $query->insert('tx_redirectmanager_not_found_log')
                ->values($entry)
                ->execute();
        } // There already is a log entry for this URL; update the existing entry
        else {
            // Increase hit count
            $entry['hit_count'] = $previousEntry['hit_count'] + 1;

            // Check if this url was marked as resolved, but reappeared again
            if ((bool)$previousEntry['is_resolved'] === true) {
                // Mark as not resolved again
                $entry['is_resolved'] = 0;

                // Increase the counter for how often this URL has reappeared
                $entry['has_reappeared_count'] = $previousEntry['has_reappeared_count'] + 1;
            }

            // Update entry in database
            $query->resetQueryParts();
            $query->update('tx_redirectmanager_not_found_log');

            foreach ($entry as $field => $value) {
                $query->set($field, $value);
            }

            $query->where(
                $query->expr()->eq('hash', $query->createNamedParameter($hash))
            )
                ->execute();
        }
    }
}
