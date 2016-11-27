<div class="col-md-8">
    {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

    {if $is_admin_auth && !$is_user_auth}
        {{ LinkCoreForm::display(t('Remove this avatar?'), null, null, null, array('del'=>1)) }}
    {else}
        {{ LinkCoreForm::display(t('Remove this avatar?'), 'user', 'setting', 'avatar', array('del'=>1)) }}
    {/if}

    {{ AvatarForm::display() }}
    <p><span class="underline err_msg">{lang 'Warning:'}</span> {lang 'Your profile photo must contain a photo of you under penalty of banishment of your account!'}</p>

    {if $is_webcam_enabled} {* Check if the module is enabled *}
        <p class="s_tMarg bold"><a href="{{ $design->url('webcam','webcam','picture') }}">{lang 'Want to take a photo of yourself directly with your webcam?'}</a></p>
    {/if}
</div>