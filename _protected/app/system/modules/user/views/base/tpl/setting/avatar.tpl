<div class="col-md-8">
    {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

    {if $is_admin_auth AND !$is_user_auth}
        {{ LinkCoreForm::display(t('Remove the profile photo?'), null, null, null, array('del'=>1)) }}
    {else}
        {{ LinkCoreForm::display(t('Remove the profile photo?'), 'user', 'setting', 'avatar', array('del'=>1)) }}
    {/if}

    {{ AvatarForm::display() }}

    <p>
        <span class="underline err_msg">{lang 'Careful'}</span> {lang 'Your profile photo must be a photo of yourself.'}
    </p>
</div>
