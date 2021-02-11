<?php

namespace Typoheads\RedirectManager\Tca;

use Ergebnis\Json\Printer\Printer;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Backend\Form\NodeFactory;

/**
 * Used to prefill the EXT:redirects souce_path TCA field with a value from a link from this extension's backend module.
 *
 * @author Philipp Seiler <ps@typoheads.at>
 */
class PrefillSourcePathFromRedirectManagerElement extends InputTextElement
{
    /**
     * @inheritDoc
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
    }



    /**
     * @inheritDoc
     */
    public function render()
    {
        // If a parameter for this extension is present in the backend URL, use it to prefill certain values
        if (!empty($_GET['source_path'])) {
            $this->data['parameterArray']['itemFormElValue'] = $_GET['source_path'];
        }

        return parent::render();
    }
}
