{if $errors}
    <div class="error">
        {section name=i loop=$errors}
            <p>{$errors[i]}</p>
        {/section}
    </div>
{/if}
