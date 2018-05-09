<div class="center">
    {if !empty($error) }
        <p class="red">{error}</p>
    {elseif sizeof($urls) > 0}
        <ul>
            {each $key => $value in $urls}
                <li>
                    <img src="{url_static_img}icon/feed.svg" alt="RSS Feed" />&nbsp;
                    <span class="underline"><a href="{key}">{value}</a></span>
                </li>
            {/each}
        </ul>
    {else}
        <p>{lang 'No RSS Feed found at %site_name%'}</p>
    {/if}
</div>
