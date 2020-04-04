{{ $design->xmlHeader() }}
{{ XmlDesign::rssHeader() }}

<channel>
  <title>{lang 'Latest Forum Topic Posts'}</title>
  <link>{current_url}</link>
  <description>{lang 'Latest Forum Topic Posts of %site_name%'}</description>

  {each $msg in $forums_messages}
    <item>
      <title>{% escape(Framework\Security\Ban\Ban::filterWord($msg->title, false)) %}</title>
      <link>
        {{ $design->url('forum', 'forum', 'post', escape("$msg->name,$msg->forumId,$msg->title,$msg->topicId") ) }}#{% $msg->messageId %}
      </link>
      <pubDate>{% DateFormat::getRss($msg->createdDate) %}</pubDate>
      {if !empty($com->updatedDate)}
        <lastBuildDate>{% DateFormat::getRss($msg->updatedDate) %}</lastBuildDate>
      {/if}
      <description><![CDATA[{% Framework\Security\Ban\Ban::filterWord($msg->message, false) %}]]></description>
    </item>
  {/each}
</channel>

{{ XmlDesign::rssFooter() }}
