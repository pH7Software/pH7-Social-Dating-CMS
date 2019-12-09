{if !$is_own_profile}
    <div class="center">
        {if $is_mail_enabled}
            <a class="s_tMarg btn btn-success btn-lg" rel="nofollow" href="{mail_link}">
                {lang '👍 Wanna Meet 😍'}
            </a>
        {elseif $is_im_enabled}
            <a class="s_tMarg btn btn-success btn-lg" rel="nofollow" href="{messenger_link}">
                {lang '👍 Wanna Speak 💬'}
            </a>
        {/if}

        {if $is_mail_enabled OR $is_im_enabled}
            <a class="s_tMarg btn btn-danger btn-lg" href="{{ $design->url('user', 'browse', 'index', '?country='.$country_code.'&sex='.$sex) }}">
                {lang ' 👎 Not Interested'}
            </a>
        {/if}
    </div>
{/if}
