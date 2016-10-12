<div class="left col-md-6">
    {include 'progressbar.inc.tpl'}
    {{ JoinForm::step1() }}
</div>

<div class="right col-md-4 animated fadeInRight">
    <p>{lang 'Already registered?'} <a href="{{ $design->url('user','main','login') }}"><strong>{lang 'Sign In!'}</strong></a></p>
    {if !empty($user_ref)}
        <div class="center">
            <a href="{{ $design->getUserAvatar($username, $sex, 400) }}" title="{first_name}" data-popup="image">
                <img class="avatar s_marg" alt="{first_name} {username}" title="{first_name}" src="{{ $design->getUserAvatar($username, $sex, 200) }}" />
            </a>
        </div>
    {else}
        <div class="s_tMarg">
            {{ $userDesignModel->profilesBlock() }}
        </div>
    {/if}
</div>
