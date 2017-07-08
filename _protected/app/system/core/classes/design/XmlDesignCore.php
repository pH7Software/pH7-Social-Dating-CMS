<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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

    public static function rssHeaderLinks()
    {
        echo '<link rel="alternate" type="application/rss+xml" title="', t('Latest Blog Posts'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'blog'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Notes'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'note'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Forum Topics'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'forum-topic'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Profile Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-profile'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Blog Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-blog'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Note Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-note'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Picture Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-picture'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Video Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-video'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Game Comments'), '" href="', Uri::get('xml', 'rss', 'xmlrouter', 'comment-game'), '" />';
    }

    /**
     * Show the software news.
     *
     * @param integet $iNum Number of news to display.
     *
     * @return void HTML contents.
     */
    public static function softwareNews($iNum)
    {
        try {
            $aNews = (new NewsFeedCore)->getSoftware($iNum);

            if (count($aNews) > 0) {
                foreach ($aNews as $aItems) {
                    echo '<h4><a href="', $aItems['link'], '" target="_blank">', escape($aItems['title'], true), '</a></h4>';
                    echo '<p>', escape($aItems['description'], true), '</p>';
                }
            } else {
                echo '<p>', t('No News Software.'), '</p>';
            }
        } catch (PH7Exception $oE) {
            (new Design)->setFlashMsg(
                t("It seems you don't have Internet connection or the remote URL is temporarily unavailable. Some features on the admin panel won't be available."),
                'error'
            );
        }
    }
}
