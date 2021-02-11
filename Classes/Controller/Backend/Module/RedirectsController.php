<?php

namespace Typoheads\RedirectManager\Controller\Backend\Module;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Typoheads\RedirectManager\Domain\Model\NotFoundLog;
use Typoheads\RedirectManager\Domain\Repository\NotFoundLogRepository;

/**
 * Controller for managing redirects.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class RedirectsController extends ActionController
{
    /**
     * Repository for "404 Not Found" log entries.
     *
     * @var \Typoheads\RedirectManager\Domain\Repository\NotFoundLogRepository
     */
    private $notFoundLogRepository;



    /**
     * RedirectsController constructor.
     *
     * @param \Typoheads\RedirectManager\Controller\Backend\Module\NotFoundLogRepository $notFoundLogRepository Repository for "404 Not Found" log entries.
     */
    public function __construct(NotFoundLogRepository $notFoundLogRepository)
    {
        $this->notFoundLogRepository = $notFoundLogRepository;
    }



    /**
     * Main entry point of the module.
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function indexAction(): void
    {
        // Current empty. As of now, only one action that actually does something exists. This action here may be used to collect all features within this
        // backend module. The user can the select the function desired.
        $this->forward('listNotFound');
    }



    /**
     * Lists all "404 Not Found" redirects.
     *
     * @return void
     */
    public function listNotFoundAction(): void
    {
        $this->view->assign('entries', $this->notFoundLogRepository->findAll());
    }



    /**
     * Delets a "404 Not Found" log entry.
     *
     * @param \Typoheads\RedirectManager\Domain\Model\NotFoundLog $entry Entry to delete
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException|\TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteNotFoundLogAction(NotFoundLog $entry): void
    {
        $this->notFoundLogRepository->remove($entry);

        // Use redirect instead of forward, otherwise cache will prevent the new list to be rendered with updated data
        $this->redirect('listNotFound');
    }



    /**
     * Marks a "404 Not Found" log entry as resolved.
     *
     * @param \Typoheads\RedirectManager\Domain\Model\NotFoundLog $entry Entry to resolve
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function resolveNotFoundLogAction(NotFoundLog $entry): void
    {
        $entry->setHasReappearedCount(0);
        $entry->setIsResolved(true);
        $this->notFoundLogRepository->update($entry);

        // Use redirect instead of forward, otherwise cache will prevent the new list to be rendered with updated data
        $this->redirect('listNotFound');
    }



    /**
     * Unmarks a "404 Not Found" log entry as resolved.
     *
     * @param \Typoheads\RedirectManager\Domain\Model\NotFoundLog $entry Entry to un-resolve
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function unresolveNotFoundLogAction(NotFoundLog $entry): void
    {
        $entry->setHasReappearedCount(0);
        $entry->setIsResolved(false);
        $this->notFoundLogRepository->update($entry);

        // Use redirect instead of forward, otherwise cache will prevent the new list to be rendered with updated data
        $this->redirect('listNotFound');
    }
}
