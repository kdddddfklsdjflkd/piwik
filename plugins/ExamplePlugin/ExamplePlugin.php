<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package Piwik_ExamplePlugin
 */
use Piwik\Plugin;
use Piwik\WidgetsList;

/**
 *
 * @package Piwik_ExamplePlugin
 */
class Piwik_ExamplePlugin extends Plugin
{
    /**
     * @see Piwik_Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
//			'Controller.renderView' => 'addUniqueVisitorsColumnToGivenReport',
            'WidgetsList.add' => 'addWidgets',
        );
    }

    function activate()
    {
        // Executed every time plugin is Enabled
    }

    function deactivate()
    {
        // Executed every time plugin is disabled
    }

    public function addUniqueVisitorsColumnToGivenReport($view)
    {
        $view = $view['view'];
        if ($view->getCurrentControllerName() == 'Referers'
            && $view->getCurrentControllerAction() == 'getWebsites'
        ) {
            $view->columns_to_display[] = 'nb_uniq_visitors';
        }
    }

    public function addWidgets()
    {
        // we register the widgets so they appear in the "Add a new widget" window in the dashboard
        // Note that the first two parameters can be either a normal string, or an index to a translation string
        WidgetsList::add('ExamplePlugin_exampleWidgets', 'ExamplePlugin_exampleWidget', 'ExamplePlugin', 'exampleWidget');
        WidgetsList::add('ExamplePlugin_exampleWidgets', 'ExamplePlugin_photostreamMatt', 'ExamplePlugin', 'photostreamMatt');
        WidgetsList::add('ExamplePlugin_exampleWidgets', 'ExamplePlugin_piwikForumVisits', 'ExamplePlugin', 'piwikDownloads');
    }
}
