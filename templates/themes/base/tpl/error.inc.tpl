<div class="center">
    <div class="empty_state">
        <span class="empty_state_icon" role="img" aria-label="{lang 'Nothing here'}">🫧</span>
        <p>{error_desc}</p>

        {if isset($pOH_not_found)}
            <div class="error-image center"></div>
            <h2>{lang 'Relax and go'} <a href="{url_root}">{lang 'home'}</a></h2>
        {/if}
    </div>
</div>
