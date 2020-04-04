{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

<url>
  <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-profile') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.5</priority>
</url>

{if $is_blog_enabled}
  <url>
    <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-blog') }}</loc>
    <lastmod>{current_date}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/if}

{if $is_note_enabled}
  <url>
    <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-note') }}</loc>
    <lastmod>{current_date}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/if}

{if $is_picture_enabled}
  <url>
    <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-picture') }}</loc>
    <lastmod>{current_date}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/if}

{if $is_video_enabled}
  <url>
    <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-video') }}</loc>
    <lastmod>{current_date}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/if}

{if $is_game_enabled}
  <url>
    <loc>{{ $design->url('xml','sitemap','xmlrouter','comment-game') }}</loc>
    <lastmod>{current_date}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/if}

{{ XmlDesign::xslFooter() }}
