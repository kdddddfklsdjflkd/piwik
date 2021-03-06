<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package Piwik_SEO
 */
use Piwik\Common;
use Piwik\DataTable\Renderer;
use Piwik\Controller;
use Piwik\View;
use Piwik\Site;

/**
 * @package Piwik_SEO
 */
class Piwik_SEO_Controller extends Controller
{
    function getRank()
    {
        $idSite = Common::getRequestVar('idSite');
        $site = new Site($idSite);

        $url = urldecode(Common::getRequestVar('url', '', 'string'));

        if (!empty($url) && strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = 'http://' . $url;
        }

        if (empty($url) || !Common::isLookLikeUrl($url)) {
            $url = $site->getMainUrl();
        }

        $dataTable = Piwik_SEO_API::getInstance()->getRank($url);

        $view = new View('@SEO/getRank');
        $view->urlToRank = Piwik_SEO_RankChecker::extractDomainFromUrl($url);

        $renderer = Renderer::factory('php');
        $renderer->setSerialize(false);
        $view->ranks = $renderer->render($dataTable);
        echo $view->render();
    }
}
