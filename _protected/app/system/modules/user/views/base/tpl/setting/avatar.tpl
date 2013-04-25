{{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}
{{ LinkCoreForm::display(t('Remove this avatar?'), 'user', 'account', 'avatar', array('del'=>1)) }}
{{ AvatarForm::display() }}
<p><span class="underline err_msg">{@lang('Warning:')@}</span> {@lang('Your avatar must contain a picture of you under penalty of banishment of your account!')@}</p>
<p><br /><strong><a href="{{$design->url('webcam','webcam','picture')}}">{@lang('Want to take a photo of yourself directly with your webcam?')@}</a></strong></p>
