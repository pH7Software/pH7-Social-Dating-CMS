{if $errors}
    <div class="error">
        {section name=key loop=$errors}
            <p>{$errors[key]}</p>
        {/section}
    </div>
{/if}
