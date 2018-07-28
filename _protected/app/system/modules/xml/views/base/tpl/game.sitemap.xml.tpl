{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $game in $games}
  <url>
    <loc>{{ $design->url('game','main','game',"$game->title,$game->gameId") }}</loc>
    <lastmod>{% DateFormat::getSitemap($game->addedDate) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
