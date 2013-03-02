{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($albums_pictures as $album)@}
<url>
  <loc>{{ $design->url('picture','main','album',"$album->username,$album->name,$album->albumId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($album->updatedDate) ? $album->updatedDate : $album->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}

{@foreach($pictures as $picture)@}
<url>
  <loc>{{ $design->url('picture','main','photo',"$picture->username,$picture->albumId,$picture->title,$picture->pictureId") }}</loc>
  <lastmod>{% DateFormat::getSitemap((!empty($picture->updatedDate) ? $picture->updatedDate : $picture->createdDate)) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
