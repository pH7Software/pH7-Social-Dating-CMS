{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $album in $albums_pictures}
  <url>
    <loc>{{ $design->url('picture','main','album',"$album->username,$album->name,$album->albumId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($album->updatedDate) ? $album->updatedDate : $album->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{each $picture in $pictures}
  <url>
    <loc>{{ $design->url('picture','main','photo',"$picture->username,$picture->albumId,$picture->title,$picture->pictureId") }}</loc>
    <lastmod>{% DateFormat::getSitemap((!empty($picture->updatedDate) ? $picture->updatedDate : $picture->createdDate)) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
