{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($forums_topics as $topic)@}
<url>
  <loc>{{ $design->url('forum','forum','post', escape("$topic->name,$topic->forumId,$topic->title,$topic->topicId") ) }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($topic->updatedDate) ? $topic->updatedDate : $topic->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
