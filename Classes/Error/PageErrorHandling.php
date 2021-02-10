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

/**
 * Provides custom error-handlers.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class PageErrorHandling implements PageErrorHandlerInterface
{
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
}
