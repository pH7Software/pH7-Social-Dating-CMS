{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}

{each $user in $members}
  <url>
    <loc>{% (new UserCore)->getProfileLink($user->username) %}</loc>
    <lastmod>{% DateFormat::getSitemap($user->joinDate) %}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
{/each}

{{ XmlDesign::xslFooter() }}
