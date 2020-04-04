{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $album in $albums_videos}
  <url>
    <loc>{{ $design->url('video','main','album',"$album->username,$album->name,$album->albumId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($album->updatedDate) ? $album->updatedDate : $album->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{each $video in $videos}
  <url>
    <loc>{{ $design->url('video','main','video',"$video->username,$video->albumId,$video->title,$video->videoId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($video->updatedDate) ? $video->updatedDate : $video->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
