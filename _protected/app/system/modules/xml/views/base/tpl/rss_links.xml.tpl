{{ $design->xmlHeader() }}

<url>
  {if $is_blog_enabled}
    <link title="{lang 'Blogs'}" url="{{ $design->url('xml','rss','xmlrouter','blog') }}" />
  {/if}

  {if $is_note_enabled}
    <link title="{lang 'Notes'}" url="{{ $design->url('xml','rss','xmlrouter','note') }}" />
  {/if}

  {if $is_forum_enabled}
    <link title="{lang 'Forum Topics'}" url="{{ $design->url('xml','rss','xmlrouter','forum-topic') }}" />
  {/if}

  <link title="{lang 'Profile Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-profile') }}" />

  {if $is_blog_enabled}
    <link title="{lang 'Blog Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-blog') }}" />
  {/if}

  {if $is_note_enabled}
    <link title="{lang 'Note Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-note') }}" />
  {/if}

  {if $is_picture_enabled}
    <link title="{lang 'Picture Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-picture') }}" />
  {/if}

  {if $is_video_enabled}
    <link title="{lang 'Video Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-video') }}" />
  {/if}

  {if $is_game_enabled}
    <link title="{lang 'Game Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-game') }}" />
  {/if}
</url>
