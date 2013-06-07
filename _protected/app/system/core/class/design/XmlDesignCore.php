<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class XmlDesignCore
{

    /**
     * @constructor
     * @desc Private constructor to prevent instantiation of class since it's a static class.
     * @access private
     */
    private function __construct() {}

    public static function xslHeader()
    {
        echo '<?xml-stylesheet type="text/xsl" href="', UriRoute::get('xml','main','xsllayout'), '"?>
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
        echo '<link rel="alternate" type="application/xml" title="Sitemap" href="', UriRoute::get('xml','sitemap','xmlrouter'), '" />';
    }

    public static function rssHeaderLinks()
    {
        echo '<link rel="alternate" type="application/rss+xml" title="', t('Latest Blog Posts'), '" href="', UriRoute::get('xml','rss','xmlrouter','blog'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Notes'), '" href="', UriRoute::get('xml','rss','xmlrouter','note'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Forum Topics'), '" href="', UriRoute::get('xml','rss','xmlrouter','forum-topic'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Profile Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-profile'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Blog Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-blog'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Note Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-note'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Picture Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-picture'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Video Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-video'), '" />
        <link rel="alternate" type="application/rss+xml" title="', t('Latest Game Comments'), '" href="', UriRoute::get('xml','rss','xmlrouter','comment-game'), '" />';
    }

    /**
     * Show the software news.
     *
     * @static
     * @return void HTML contents.
     */
    public static function softwareNews()
    {
        $aNews = (new NewsFeedCore)->getSoftware();

        if (sizeof($aNews) > 0)
        {
            foreach($aNews as $aItems)
            {
                echo '<h4><a href="', $aItems['link'], '" target="_blank">', escape($aItems['title'], true), '</a></h4>';
                echo '<p>', escape($aItems['description'], true), '</p>';
            }
        }
        else
        {
            echo '<p>', t('No News Software.'), '</p>';
        }
    }

}
