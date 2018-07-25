{{ $design->xmlHeader() }}
<xsl:stylesheet
    version="2.0"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    >

    <!-- Output in HTML5 with doctype-system="about:legacy-compat" -->
    <xsl:output method="html" doctype-system="about:legacy-compat" encoding="utf-8" indent="yes" />
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta charset="utf-8" />
            <title>{lang 'XML Sitemap - %site_name%'}</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
            <link rel="stylesheet" href="{url_tpl_mod_css}style.css" />
        </head>
        <body>
        <header>
            <h1>{lang 'Site Map'} - <a href="{url_root}">{site_name}</a></h1>
        </header>
        <section>
            <table>
                <tr class="border-bottom">
                    <th>{lang 'URL'}</th>
                    <th>{lang 'Priority'}</th>
                    <th>{lang 'Change Frequency'}</th>
                    <th>{lang 'Last Change (UTC)'}</th>
                </tr>
                <xsl:variable name="alpha_lower" select="'abcdefghijklmnopqrstuvwxyz'" />
                <xsl:variable name="alpha_upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
                <xsl:for-each select="sitemap:urlset/sitemap:url">
                    <xsl:sort select="sitemap:priority" order="descending" />
                    <xsl:sort select="sitemap:lastmod" order="descending" />
                    <tr>
                        <xsl:if test="position() mod 2 != 1">
                            <xsl:attribute name="class">high</xsl:attribute>
                        </xsl:if>
                        <td>
                            <xsl:variable name="itemUrl">
                                <xsl:value-of select="sitemap:loc" />
                            </xsl:variable>
                            <a href="{$itemUrl}">
                                <xsl:value-of select="sitemap:loc" />
                            </a>
                        </td>
                        <td>
                            <xsl:value-of select="concat(sitemap:priority*100,'%')" />
                        </td>
                        <td>
                            <xsl:value-of
                                select="concat(translate(substring(sitemap:changefreq, 1, 1),concat($alpha_lower, $alpha_upper),concat($alpha_upper, $alpha_lower)),substring(sitemap:changefreq, 2))" />
                        </td>
                        <td>
                            <xsl:value-of
                                select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))" />
                        </td>
                    </tr>
                </xsl:for-each>
            </table>
        </section>
        <footer>
            <p>{{ $design->smallLink() }}</p>
        </footer>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
