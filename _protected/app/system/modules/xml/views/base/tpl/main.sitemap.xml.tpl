{{ $design->xmlHeader() }}
{{ XmlDesign::xslHeader() }}


<url>
  <loc>{url_root}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>daily</changefreq>
  <priority>1.0</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.8</priority>
</url>

<url>
  <loc>{{ $design->url('webcam','webcam','picture') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>

<url>
  <loc>{{ $design->url('forum','forum','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.6</priority>
</url>

<url>
  <loc>{{ $design->url('picture','main','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.5</priority>
</url>

<url>
  <loc>{{ $design->url('video','main','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('chat','home','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('chatroulette','home','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('blog','main','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.8</priority>
</url>

<url>
  <loc>{{ $design->url('game','main','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','about') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.7</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','helpus') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','sharesite') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>yearly</changefreq>
  <priority>0.3</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','faq') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.4</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','job') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.4</priority>
</url>

<url>
  <loc>{{ $design->url('page','main','team') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>yearly</changefreq>
  <priority>0.3</priority>
</url>

<url>
  <loc>{{ $design->url('affiliate','home','index') }}</loc>
  <lastmod>{current_date}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>0.4</priority>
</url>


{{ XmlDesign::xslFooter() }}
