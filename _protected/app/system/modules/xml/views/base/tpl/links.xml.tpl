{{ $design->xmlHeader() }}

<url>
    <link title="{lang 'About %site_name%'}" url="{{ $design->url('page','main','index') }}" />

    {if $is_webcam_enabled}
        <link title="{lang 'Webcam Picture Party Fun'}" url="{{ $design->url('webcam','webcam','picture') }}" />
    {/if}

    {if $is_forum_enabled}
        <link title="{lang 'Discussion Forums'}" url="{{ $design->url('forum','forum','index') }}" />
    {/if}

    {if $is_picture_enabled}
        <link title="{lang 'Photo Gallery'}" url="{{ $design->url('picture','main','index') }}" />
    {/if}

    {if $is_video_enabled}
        <link title="{lang 'Videos Movie'}" url="{{ $design->url('video','main','index') }}" />
    {/if}

    {if $is_chat_enabled}
        <link title="{lang 'Chat Rooms'}" url="{{ $design->url('chat','home','index') }}" />
    {/if}

    {if $is_chatroulette_enabled}
        <link title="{lang 'Free Chatroulette Speed Dating'}" url="{{ $design->url('chatroulette','home','index') }}" />
    {/if}

    {if $is_blog_enabled}
        <link title="{lang 'Blog'}" url="{{ $design->url('blog','main','index') }}" />
    {/if}

    {if $is_note_enabled}
        <link title="{lang 'Community Notes'}" url="{{ $design->url('note','main','index') }}" />
    {/if}

    {if $is_game_enabled}
        <link title="{lang 'Games Zone Party'}" url="{{ $design->url('game','main','index') }}" />
    {/if}

    {if $is_birthday_enabled}
        <link title="{lang 'User Birthday'}" url="{{ $design->url('birthday','user','index') }}" />
    {/if}

    {if $is_map_enabled}
        <link title="{lang 'People Nearby'}" url="{{ $design->url('map','country','index') }}" />
    {/if}

    <link title="{lang 'About Us'}" url="{{ $design->url('page','main','about') }}" />
    <link title="{lang 'Help Us'}" url="{{ $design->url('page','main','helpus') }}" />
    <link title="{lang 'Share %site_name%'}" url="{{ $design->url('page','main','sharesite') }}" />
    <link title="{lang 'FAQ'}" url="{{ $design->url('page','main','faq') }}" />
    {if $is_affiliate_enabled}
        <link title="{lang 'Affiliate'}" url="{{ $design->url('affiliate','home','index') }}" />
    {/if}
    <link title="{lang 'Jobs'}" url="{{ $design->url('page','main','job') }}" />
    <link title="{lang 'RSS Feed List'}" url="{{ $design->url('xml','rss','index') }}" />
    <link title="{lang 'XML Site Map'}" url="{{ $design->url('xml','sitemap','xmlrouter') }}" />
</url>
