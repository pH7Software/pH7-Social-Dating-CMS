{{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

{if AdminCore::auth()}
  {{ LinkCoreForm::display(t('Remove this avatar?'), null, null, null, array('del'=>1)) }}
{else}
  {{ LinkCoreForm::display(t('Remove this avatar?'), 'user', 'setting', 'avatar', array('del'=>1)) }}
{/if}

{{ AvatarForm::display() }}
<p><span class="underline err_msg">{lang 'Warning:'}</span> {lang 'Your avatar must contain a picture of you under penalty of banishment of your account!'}</p>
<p><br /><strong><a href="{{ $design->url('webcam','webcam','picture') }}">{lang 'Want to take a photo of yourself directly with your webcam?'}</a></strong></p>
