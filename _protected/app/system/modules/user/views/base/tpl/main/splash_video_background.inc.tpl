{* Count the number of different splash videos *}
{{ $total_videos = count(glob(PH7_PATH_TPL . PH7_TPL_NAME . '/file/splash/*_vid.jpg')) }}
{{ $video_prefix = mt_rand(1, $total_videos) }}

{if !$browser->isMobile()}
    {* The background video is enabled only if visitors aren't from mobile devices (for performance reasons...) *}
    <style scoped="scoped">video#bgvid{background: url({url_tpl}file/splash/{video_prefix}_vid.jpg) no-repeat center}</style>
    <video autoplay loop muted poster="{url_tpl}file/splash/{video_prefix}_vid.jpg" id="bgvid">
        <source src="{url_tpl}file/splash/{video_prefix}_vid.webm" type="video/webm" />
        <source src="{url_tpl}file/splash/{video_prefix}_vid.mp4" type="video/mp4" />
    </video>
{else}
    <style scoped="scoped">
        body {
            background: url('{url_tpl}file/splash/{video_prefix}_vid.jpg') repeat-y center;
            background-size: cover;
            top: 50%;
            left: 50%;
        }
    </style>
{/if}
