{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $com in $comments}
  <url>
    <loc>{{ $design->url('comment','comment','read',"$table,$com->recipient") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($com->updatedDate) ? $com->updatedDate : $com->createdDate)) %}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.5</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
