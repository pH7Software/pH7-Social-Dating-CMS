{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

<url>
  <loc>{{ $design->url('xml','sitemap','xmlrouter','forum') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('xml','sitemap','xmlrouter','forum-topic') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.7</priority>
</url>

{{ XmlDesign::xslFooter() }}

