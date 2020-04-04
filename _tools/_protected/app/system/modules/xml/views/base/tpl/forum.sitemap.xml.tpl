{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $forum in $forums}
  <url>
    <loc>{{ $design->url('forum','forum','topic',"$forum->name,$forum->forumId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($forum->updatedDate) ? $forum->updatedDate : $forum->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
