{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($games as $game)@}
<url>
  <loc>{{ $design->url('game','main','game',"$game->title,$game->gameId") }}</loc>
  <lastmod>{% DateFormat::getSitemap($game->addedDate) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.7</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
