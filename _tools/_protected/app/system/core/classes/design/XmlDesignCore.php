<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7Exception;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Pattern\Statik;

class XmlDesignCore
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    public static function xslHeader()
    {
        echo '<?xml-stylesheet type="text/xsl" href="', Uri::get('xml', 'main', 'xsllayout'), '"?>
        <urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    public static function rssHeader()
    {
        echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">';
    }

    public static function xslFooter()
    {
        echo '</urlset>';
    }

    public static function rssFooter()
    {
        echo '</rss>';
    }

    public static function sitemapHeaderLink()
    {
        echo '<link rel="alternate" type="application/xml" title="Sitemap" href="', Uri::get('xml', 'sitemap', 'xmlrouter'), '" />';
    }

    /**
     * @internal Normally, we should display each link only if the module is enabled, but for optimization reasons,
     * we don't do it since it doesn't really matter for this section.
     *
     * @return void
     *
     * @throws Framework\File\IOException
     */
    public static function rssHeaderLinks()
    {
        self::generateRssTagLink(t('Latest Blog Posts'), Uri::get('xml', 'rss', 'xmlrouter', 'blog'));
        self::generateRssTagLink(t('Latest Blog Posts'), Uri::get('xml', 'rss', 'xmlrouter', 'blog'));
        self::generateRssTagLink(t('Latest Notes'), Uri::get('xml', 'rss', 'xmlrouter', 'note'));
        self::generateRssTagLink(t('Latest Forum Topics'), Uri::get('xml', 'rss', 'xmlrouter', 'forum-topic'));
        self::generateRssTagLink(t('Latest Profile Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-profile'));
        self::generateRssTagLink(t('Latest Blog Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-blog'));
        self::generateRssTagLink(t('Latest Note Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-note'));
        self::generateRssTagLink(t('Latest Picture Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-picture'));
        self::generateRssTagLink(t('Latest Video Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-video'));
        self::generateRssTagLink(t('Latest Game Comments'), Uri::get('xml', 'rss', 'xmlrouter', 'comment-game'));
    }

    /**
     * Show the software news.
     *
     * @param int $iNum Number of news to display.
     *
     * @return void HTML contents.
     */
    public static function softwareNews($iNum)
    {
        try {
            $aNews = (new NewsFeedCore)->getSoftware($iNum);

            if (count($aNews) > 0) {
                foreach ($aNews as $aItems) {
                    echo '<h4><a href="', $aItems['link'], '" target="_blank" rel="noopener">', escape($aItems['title'], true), '</a></h4>';
                    echo '<p>', escape($aItems['description'], true), '</p>';
                }
            } else {
                echo '<p>', t("No %software_name%'s news found."), '</p>';
            }
        } catch (PH7Exception $oE) {
            (new Design)->setFlashMsg(
                t("It seems you don't have Internet (or pH7CMS feed news is temporarily unavailable). Some features on the dashboard won't be available."),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * @param string $sTitle
     * @param string $sUrl
     *
     * @return void HTML output.
     */
    private static function generateRssTagLink($sTitle, $sUrl)
    {
        echo '<link rel="alternate" type="application/rss+xml" title="', $sTitle, '" href="', $sUrl, '" />';
    }
}
