<div class="center">

<div class="left">
<div id="progress_bar"><label id="percent"></label></div>
{{ JoinForm::step1() }}
</div>

<div class="right">
<p>{lang 'Already registered?'} <a href="{{$design->url('user','main','login')}}"><strong>{lang 'Sign In!'}</strong></a></p>
{if !empty($user_ref)}
<a href="{{ $design->getUserAvatar($username, $sex, 400) }}" title="{first_name}" data-popup="image"><img class="avatar s_marg" alt="{first_name} {username}" title="{first_name}" src="{{ $design->getUserAvatar($username, $sex, 200) }}" /></a>
{else}
<br />
{{ $userDesignModel->profilesBlock() }}
{/if}
</div>

</div>

<script>$('#progress_bar').progressbar({value:33});$('#progress_bar').css('width','300px');$('#percent').text('33% - STEP 1/3');</script>
