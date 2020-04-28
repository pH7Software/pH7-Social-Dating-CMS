<div class="center" id="visitor_block">
    {if $user_views_setting == PrivacyCore::NO}
        <div class="center alert alert-warning">{lang 'To see the new members who view your profile, you must first change'} <a href="{{ $design->url('user','setting','privacy') }}">{lang 'your privacy settings'}</a>.</div>
    {/if}

    {if empty($error)}
        <h3 class="underline">{lang 'Recently Viewed By:'}</h3>
        <p class="italic underline"><strong><a href="{{ $design->url('user','visitor','index',$username) }}">{visitor_number}</a></strong></p><br />
        {each $v in $visitors}
            <div class="s_photo">
                {{ $avatarDesign->get($v->username, $v->firstName, $v->sex, 64, $bRollover = true) }}
            </div>
        {/each}

        {main_include 'page_nav.inc.tpl'}
        <br />
        <p class="center bottom">
            <a class="btn btn-default btn-md" href="{{ $design->url('user','visitor','search',$username) }}">{lang 'Search for a visitor of %0%', $v->username}</a>
        </p>
    {else}
        <p>{error}</p>
    {/if}
</div>
