<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik
 * @package Piwik
 */

namespace Piwik\Visualization\JqplotGraph;

use Piwik\Common;
use Piwik\Site;
use Piwik\Controller;
use Piwik\Period\Range;
use Piwik\Visualization\JqplotGraph;
use Piwik\JqplotDataGenerator;

/**
 * TODO
 */
class Evolution extends JqplotGraph
{
    const GRAPH_HEIGHT = 170;
    const ID = 'graphEvolution';

    public function __construct($view)
    {
        parent::__construct($view);
        $view->datatable_css_class = 'dataTableEvolutionGraph';

        // period will be overridden when 'range' is requested in the UI // TODO: this code probably shouldn't be here...
        // but the graph will display for each day of the range.
        // Default 'range' behavior is to return the 'sum' for the range
        if (Common::getRequestVar('period', false) == 'range') {
            $view->request_parameters_to_modify['period'] = 'day';
        }

        $this->calculateEvolutionDateRange($view);
    }

    protected function getDefaultPropertyValues($view)
    {
        $result = parent::getDefaultPropertyValues($view);
        $result['graph_height'] = self::GRAPH_HEIGHT . 'px';
        $result['show_all_views_icons'] = false;
        $result['show_table'] = false;
        $result['show_table'] = false;
        $result['show_table_all_columns'] = false;
        $result['hide_annotations_view'] = false;
        return $result;
    }

    protected function makeDataGenerator($properties)
    {
        return JqplotDataGenerator::factory('evolution', $properties);
    }

    /**
     * Based on the period, date and evolution_{$period}_last_n query parameters,
     * calculates the date range this evolution chart will display data for.
     */
    private function calculateEvolutionDateRange(&$view)
    {
        $period = Common::getRequestVar('period');

        $defaultLastN = self::getDefaultLastN($period);
        $originalDate = Common::getRequestVar('date', 'last' . $defaultLastN, 'string');

        if ($period != 'range') { // show evolution limit if the period is not a range
            $view->show_limit_control = true;

            // set the evolution_{$period}_last_n query param
            if (Range::parseDateRange($originalDate)) { // if a multiple period
                // overwrite last_n param using the date range
                $oPeriod = new Range($period, $originalDate);
                $lastN = count($oPeriod->getSubperiods());
            } else { // if not a multiple period
                list($newDate, $lastN) = self::getDateRangeAndLastN($period, $originalDate, $defaultLastN);
                $view->request_parameters_to_modify['date'] = $newDate;
                $view->custom_parameters['dateUsedInGraph'] = $newDate;
            }
            $lastNParamName = self::getLastNParamName($period);
            $view->custom_parameters[$lastNParamName] = $lastN;
        }
    }

    /**
     * Returns the entire date range and lastN value for the current request, based on
     * a period type and end date.
     *
     * @param string $period The period type, 'day', 'week', 'month' or 'year'
     * @param string $endDate The end date.
     * @param int|null $defaultLastN The default lastN to use. If null, the result of
     *                               getDefaultLastN is used.
     * @return array An array w/ two elements. The first is a whole date range and the second
     *               is the lastN number used, ie, array('2010-01-01,2012-01-02', 2).
     */
    public static function getDateRangeAndLastN($period, $endDate, $defaultLastN = null)
    {
        if ($defaultLastN === null) {
            $defaultLastN = self::getDefaultLastN($period);
        }

        $lastNParamName = self::getLastNParamName($period);
        $lastN = Common::getRequestVar($lastNParamName, $defaultLastN, 'int');

        $site = new Site(Common::getRequestVar('idSite'));

        $dateRange = Controller::getDateRangeRelativeToEndDate($period, 'last' . $lastN, $endDate, $site);

        return array($dateRange, $lastN);
    }

    /**
     * Returns the default last N number of dates to display for a given period.
     *
     * @param string $period 'day', 'week', 'month' or 'year'
     * @return int
     */
    public static function getDefaultLastN($period)
    {
        switch ($period) {
            case 'week':
                return 26;
            case 'month':
                return 24;
            case 'year':
                return 5;
            case 'day':
            default:
                return 30;
        }
    }

    /**
     * Returns the query parameter that stores the lastN number of periods to get for
     * the evolution graph.
     *
     * @param string $period The period type, 'day', 'week', 'month' or 'year'.
     * @return string
     */
    public static function getLastNParamName($period)
    {
        return "evolution_{$period}_last_n";
    }
}