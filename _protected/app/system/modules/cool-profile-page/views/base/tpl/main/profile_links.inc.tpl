<div class="row">
    {if $is_im_enabled AND !$is_own_profile}
        <a class="vs_marg" rel="nofollow" href="{messenger_link}" title="{lang 'Chat'}">
            <i class="fa fa-comment-o chat"></i>
        </a>
    {/if}

    {if $is_lovecalculator_enabled AND !$is_own_profile}
        <a class="vs_marg" href="{{ $design->url('love-calculator','main','index',$username) }}" title="{lang 'Match'}">
            <i class="fa fa-heart-o heart"></i>
        </a>
    {/if}
</div>

<div class="row">
    {if $is_mail_enabled AND !$is_own_profile}
        <a class="vs_marg" rel="nofollow" href="{mail_link}" title="{lang 'Send Message'}">
            <li class="fa fa-envelope-o message"></li>
        </a>
    {/if}

    {if $is_friend_enabled AND !$is_own_profile}
        <a class="vs_marg" rel="nofollow" href="{friend_link}">
            {if $is_approved_friend}
                <i class="fa fa-user-times friend"></i>
            {elseif $is_pending_friend}
                <i class="fa fa-users friend"></i>
            {else}
                <i class="fa fa-user-plus friend"></i>
            {/if}
        </a>
    {/if}
</div>
