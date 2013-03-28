{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($albums_videos as $album)@}
<url>
  <loc>{{ $design->url('video','main','album',"$album->username,$album->name,$album->albumId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($album->updatedDate) ? $album->updatedDate : $album->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}

{@foreach($videos as $video)@}
<url>
  <loc>{{ $design->url('video','main','video',"$video->username,$video->albumId,$video->title,$video->videoId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($video->updatedDate) ? $video->updatedDate : $video->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
