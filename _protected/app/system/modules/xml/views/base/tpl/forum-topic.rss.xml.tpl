{{ $design->xmlHeader() }}
{{ XmlDesign::rssHeader() }}

<channel>
  <title>{lang 'Latest Forum Topics'}</title>
  <link>{{ $design->url('forum','forum','index') }}</link>
  <description>{lang 'Latest Forum Topics of %site_name%'}</description>

  {each $topic in $forums_topics}
    <item>
      <title>{% escape(Framework\Security\Ban\Ban::filterWord($topic->title, false)) %}</title>
      <link>{{ $design->url('forum','forum','post', escape("$topic->name,$topic->forumId,$topic->title,$topic->topicId") ) }}</link>
      <pubDate>{% DateFormat::getRss($topic->createdDate) %}</pubDate>
      {if !empty($com->updatedDate)}
        <lastBuildDate>{% DateFormat::getRss($topic->updatedDate) %}</lastBuildDate>
      {/if}
      <description><![CDATA[{% Framework\Security\Ban\Ban::filterWord($topic->message, false) %}]]></description>
    </item>
  {/each}
</channel>

{{ XmlDesign::rssFooter() }}
