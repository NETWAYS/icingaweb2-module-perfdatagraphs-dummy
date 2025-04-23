<?php

namespace Icinga\Module\Perfdatagraphsdummy\Controllers;

use Icinga\Module\Perfdatagraphsdummy\Forms\PerfdataGraphsDummyConfigForm;

use Icinga\Application\Config;
use ipl\Html\HtmlString;
use Icinga\Web\Widget\Tabs;

use ipl\Web\Compat\CompatController;

/**
 * ConfigController manages the configuration for the PerfdataGraphs Dummy Module.
 */
class ConfigController extends CompatController
{
    protected bool $disableDefaultAutoRefresh = true;

    /**
     * Initialize the Controller.
     */
    public function init(): void
    {
        // Assert the user has access to this controller.
        $this->assertPermission('config/modules');
        parent::init();
    }

    /**
     * generalAction provides the configuration form.
     * For now we have everything on a single Tab, might be extended in the future.
     */
    public function generalAction(): void
    {
        $form = (new PerfdataGraphsDummyConfigForm())
            ->setIniConfig(Config::module('perfdatagraphsdummy'));
        $form->handleRequest();
        $this->addContent(new HtmlString($form->render()));
    }

    /**
     * Merge tabs with other tabs contained in this tab panel.
     *
     * @param Tabs $tabs
     */
    protected function mergeTabs(Tabs $tabs): void
    {
        foreach ($tabs->getTabs() as $tab) {
            $this->tabs->add($tab->getName(), $tab);
        }
    }
}
