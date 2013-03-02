{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($blogs as $post)@}
<url>
  <loc>{{ $design->url('blog','main','read',$post->postId) }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($post->updatedDate) ? $post->updatedDate : $post->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
