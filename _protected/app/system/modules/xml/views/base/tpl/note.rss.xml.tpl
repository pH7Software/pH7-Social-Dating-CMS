{{ $design->xmlHeader() }}
{{ XmlDesign::rssHeader() }}

<channel>
  <title>{lang 'Latest Notes'}</title>
  <link>{{ $design->url('note','main','index') }}</link>
  <description>{lang 'Latest Notes %site_name%'}</description>

  {each $post in $notes}
    <item>
      <title>{% escape(Framework\Security\Ban\Ban::filterWord($post->pageTitle, false)) %}</title>
      <link>{{ $design->url('note','main','read',"$post->username,$post->postId") }}</link>
      <pubDate>{% DateFormat::getRss($post->createdDate) %}</pubDate>
      {if !empty($post->updatedDate)}
        <lastBuildDate>{% DateFormat::getRss($post->updatedDate) %}</lastBuildDate>
      {/if}
      <description><![CDATA[{% escape(Framework\Security\Ban\Ban::filterWord($post->content, false)) %}]]></description>
      <language>{% escape($post->langId) %}</language>
      <copyright>{% escape(Framework\Security\Ban\Ban::filterWord($post->metaCopyright, false)) %}</copyright>
      <dc:creator>{% escape($post->firstName) %}{if !empty($post->lastName)} {% escape($post->lastName) %} {/if}</dc:creator>
    </item>
  {/each}
</channel>

{{ XmlDesign::rssFooter() }}
