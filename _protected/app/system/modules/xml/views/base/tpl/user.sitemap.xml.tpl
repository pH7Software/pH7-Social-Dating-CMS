{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


{@foreach($members as $user)@}
<url>
  <loc>{% (new UserCore)->getProfileLink($user->username) %}</loc>
  <lastmod>{% DateFormat::getSitemap($user->joinDate) %}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
{@/foreach@}


{{ XmlDesign::xslFooter() }}
