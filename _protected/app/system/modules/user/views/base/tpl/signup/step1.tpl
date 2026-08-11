<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
    <div class="pull-left col-xs-12 col-sm-7 col-md-6 col-lg-7 animated fadeInLeft">
        {include 'progressbar.inc.tpl'}
        {{ JoinForm::step1() }}
    </div>

    <div class="pull-right col-xs-12 col-sm-5 col-md-5 col-md-offset-1 col-lg-4 animated fadeInRight">
        <aside class="panel panel-default signup_support" aria-label="{lang 'Signup information'}">
            <div class="panel-body">
                <h2>{lang 'Create your profile with confidence'}</h2>
                <ul class="list-unstyled">
                    <li><i class="fa fa-check-circle" aria-hidden="true"></i> {lang 'Registration takes three short steps.'}</li>
                    <li><i class="fa fa-check-circle" aria-hidden="true"></i> {lang 'You can update your profile, privacy, and notification settings later.'}</li>
                </ul>
                <p>
                    {lang 'Already have an account?'}
                    <a href="{{ $design->url('user','main','login') }}"><strong>{lang 'Sign in'}</strong></a>
                </p>
            </div>
        </aside>

        <div class="center signup_profiles">
            {if !empty($user_ref)}
                <a href="{{ $design->getUserAvatar($username, $sex, 400) }}" title="{first_name}" data-popup="image">
                    <img
                        class="avatar s_marg"
                        alt="{first_name} {username}"
                        title="{first_name}"
                        src="{{ $design->getUserAvatar($username, $sex, 400) }}"
                    />
                </a>
            {else}
                <div class="s_tMarg">
                    {{ $userDesignModel->profilesBlock() }}
                </div>
            {/if}
        </div>
    </div>
</div>
