<div class="center">
    {if !empty($error) }
        <p class="red">{error}</p>
    {elseif sizeof($urls) > 0}
        <ul>
            {each $key => $value in $urls}
                <li class="underline">
                    <a href="{key}">{value}</a>
                </li>
            {/each}
        </ul>
    {else}
        <p>{lang 'Oops! No links to display. Come back later ;)'}</p>
    {/if}
</div>
