{{ $design->xmlHeader() }}
{{ XmlDesign::rssHeader() }}

<channel>
  <title>{lang 'Latest %0% Comments', ucfirst($table)}</title>
  <link>{current_url}</link>
  <description>{lang 'Latest Blog Posts %site_name%'}</description>

  {each $com in $comments}
    <item>
      <title>{lang "%0%'s comments", $com->username}</title>
      <link>{{ $design->url('comment','comment','post',"$table,$com->commentId") }}</link>
      <pubDate>{% DateFormat::getRss($com->createdDate) %}</pubDate>
      {if !empty($com->updatedDate)}
        <lastBuildDate>{% DateFormat::getRss($com->updatedDate) %}</lastBuildDate>
      {/if}
      <description><![CDATA[{% Framework\Security\Ban\Ban::filterWord($com->comment, false) %}]]></description>
    </item>
  {/each}
</channel>

{{ XmlDesign::rssFooter() }}
