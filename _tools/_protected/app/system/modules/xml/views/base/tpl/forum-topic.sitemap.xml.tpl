{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $topic in $forums_topics}
  <url>
    <loc>{{ $design->url('forum','forum','post', escape("$topic->name,$topic->forumId,$topic->title,$topic->topicId") ) }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($topic->updatedDate) ? $topic->updatedDate : $topic->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
