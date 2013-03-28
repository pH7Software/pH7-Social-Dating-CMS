{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{@foreach($forums as $forum)@}
<url>
  <loc>{{ $design->url('forum','forum','topic',"$forum->name,$forum->forumId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($forum->updatedDate) ? $forum->updatedDate : $forum->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}

{{ XmlDesign::xslFooter() }}
