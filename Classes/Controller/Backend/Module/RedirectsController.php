<?php

namespace Typoheads\RedirectManager\Controller\Backend\Module;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use Typoheads\RedirectManager\Domain\Model\NotFoundLog;
use Typoheads\RedirectManager\Domain\Repository\Demand;
use Typoheads\RedirectManager\Domain\Repository\NotFoundLogRepository;

/**
 * Controller for managing redirects.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class RedirectsController extends ActionController
{
    /**
     * Backend template container.
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * Repository for "404 Not Found" log entries.
     *
     * @var \Typoheads\RedirectManager\Domain\Repository\NotFoundLogRepository
     */
    private $notFoundLogRepository;



    /**
     * RedirectsController constructor.
     *
     * @param \Typoheads\RedirectManager\Domain\Repository\NotFoundLogRepository $notFoundLogRepository Repository for "404 Not Found" log entries.
     */
    public function __construct(NotFoundLogRepository $notFoundLogRepository)
    {
        $this->notFoundLogRepository = $notFoundLogRepository;
    }



    /**
     * Setup backend template.
     *
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var \TYPO3\CMS\Backend\View\BackendTemplateView $view */
        parent::initializeView($view);

        // Adjust backend template for view that provide UIs to the user
        if (in_array($this->actionMethodName, ['indexAction', 'listNotFoundAction'])) {
            $view->getModuleTemplate()->getPageRenderer()->addCssFile('EXT:redirect_manager/Resources/Public/Css/Backend/backend.min.css');
            $this->registerDocheaderButtons($view);
        }
    }



    /**
     * Registers buttons for the docheader-section of the backend module.
     *
     * @param \TYPO3\CMS\Backend\View\BackendTemplateView $view
     *
     * @return void
     */
    private function registerDocheaderButtons(BackendTemplateView $view): void
    {
        $buttonBar = $view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $helpButton = $buttonBar->makeHelpButton()
            ->setModuleName('_MOD_site_redirectmanager_listnotfound')
            ->setFieldName('');

        $buttonBar->addButton($helpButton);
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
        $demand = Demand::createFromRequest($this->request);
        $this->notFoundLogRepository->setDemand($demand);
        $count = $this->notFoundLogRepository->countRedirectsByByDemand();
        $this->view->assignMultiple([
            'entries' => $this->notFoundLogRepository->findByDemand(),
            'pagination' => $this->preparePagination($demand, $count),
            'demand' => $demand,
            'sorting' => $_POST['sorting']
        ]);
    }

    /**
     * Prepares information for the pagination of the module
     *
     * @param Demand $demand
     * @param int $count
     * @return array
     */
    protected function preparePagination(Demand $demand, int $count): array
    {
        $numberOfPages = ceil($count / $demand->getLimit());
        $endRecord = $demand->getOffset() + $demand->getLimit();
        if ($endRecord > $count) {
            $endRecord = $count;
        }

        $pagination = [
            'current' => $demand->getPage(),
            'numberOfPages' => $numberOfPages,
            'hasLessPages' => $demand->getPage() > 1,
            'hasMorePages' => $demand->getPage() < $numberOfPages,
            'startRecord' => $demand->getOffset() + 1,
            'endRecord' => $endRecord
        ];
        if ($pagination['current'] < $pagination['numberOfPages']) {
            $pagination['nextPage'] = $pagination['current'] + 1;
        }
        if ($pagination['current'] > 1) {
            $pagination['previousPage'] = $pagination['current'] - 1;
        }
        return $pagination;
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
