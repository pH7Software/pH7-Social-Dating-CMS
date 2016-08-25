<div class="left col-md-6">
    {include 'progressbar.inc.tpl'}
    {{ JoinForm::step1() }}
</div>

<div class="right col-md-4 animated fadeInRight">
    <p>{lang 'Already registered?'} <a href="{{ $design->url('user','main','login') }}"><strong>{lang 'Sign In!'}</strong></a></p>
    {if !empty($user_ref)}
        <a href="{{ $design->getUserAvatar($username, $sex, 400) }}" title="{first_name}" data-popup="image"><img class="avatar s_marg" alt="{first_name} {username}" title="{first_name}" src="{{ $design->getUserAvatar($username, $sex, 200) }}" /></a>
            <div class="ad_200_200">{{ $designModel->ad(200,200) }}</div>
    {else}
        <br />
        {{ $userDesignModel->profilesBlock() }}
    {/if}
</div>
