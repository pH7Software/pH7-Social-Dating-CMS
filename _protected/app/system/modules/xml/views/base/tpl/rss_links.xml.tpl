{{ $design->xmlHeader() }}

<url>
<link title="{lang 'Blogs'}" url="{{ $design->url('xml','rss','xmlrouter','blog') }}" />
<link title="{lang 'Notes'}" url="{{ $design->url('xml','rss','xmlrouter','note') }}" />
<link title="{lang 'Forum Topics'}" url="{{ $design->url('xml','rss','xmlrouter','forum-topic') }}" />

<link title="{lang 'Profile Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-profile') }}" />
<link title="{lang 'Blog Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-blog') }}" />
<link title="{lang 'Note Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-note') }}" />
<link title="{lang 'Picture Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-picture') }}" />
<link title="{lang 'Video Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-video') }}" />
<link title="{lang 'Game Comments'}" url="{{ $design->url('xml','rss','xmlrouter','comment-game') }}" />
</url>
