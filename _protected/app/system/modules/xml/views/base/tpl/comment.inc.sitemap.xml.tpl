{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($comments as $com)@}
<url>
  <loc>{{ $design->url('comment','comment','read',"$table,$com->recipient") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($com->updatedDate) ? $com->updatedDate : $com->createdDate)) %}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.5</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
