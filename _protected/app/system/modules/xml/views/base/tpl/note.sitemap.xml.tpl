{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($notes as $post)@}
<url>
  <loc>{{ $design->url('note','main','read',"$post->username,$post->postId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($post->updatedDate) ? $post->updatedDate : $post->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
