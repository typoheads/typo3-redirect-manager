<?php

namespace Typoheads\RedirectManager\Controller\Backend\Module;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for managing redirects.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class RedirectsController extends ActionController
{
    /**
     * Main entry point of the module.
     *
     * @return void
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

    }
}
