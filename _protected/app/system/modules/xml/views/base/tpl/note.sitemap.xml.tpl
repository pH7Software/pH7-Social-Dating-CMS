{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $post in $notes}
  <url>
    <loc>{{ $design->url('note','main','read',"$post->username,$post->postId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($post->updatedDate) ? $post->updatedDate : $post->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
