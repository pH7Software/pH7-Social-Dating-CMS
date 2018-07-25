{{ $design->xmlHeader() }}
{{ XmlDesign::rssHeader() }}

<channel>
  <title>{lang 'Latest Blog Posts'}</title>
  <link>{{ $design->url('blog','main','index') }}</link>
  <description>{lang 'Latest Blog Posts %site_name%'}</description>

  {each $post in $blogs}
    <item>
      <title>{% escape($post->pageTitle) %}</title>
      <link>{{ $design->url('blog','main','read',$post->postId) }}</link>
      <pubDate>{% DateFormat::getRss($post->createdDate) %}</pubDate>
      {if !empty($post->updatedDate)}
        <lastBuildDate>{% DateFormat::getRss($post->updatedDate) %}</lastBuildDate>
      {/if}
      <description><![CDATA[{% $post->content %}]]></description>
      <language>{% $post->langId %}</language>
      <copyright>{% $post->metaCopyright %}</copyright>
    </item>
  {/each}
</channel>

{{ XmlDesign::rssFooter() }}
