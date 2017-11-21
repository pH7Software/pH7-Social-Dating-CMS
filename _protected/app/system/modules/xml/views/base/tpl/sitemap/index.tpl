<div class="center">
    {if !empty($error) }
        <p>{error}</p>
    {elseif sizeof($urls) > 0}
        <ul>
            {each $key => $value in $urls}
                <li class="underline"><a href="{key}">{value}</a></li>
            {/each}
        </ul>
        {else}

    <p>{lang 'No links found at %site_name%'}</p>
    {/if}
</div>
