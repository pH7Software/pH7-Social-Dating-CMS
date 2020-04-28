<div class="center" id="friend_block">
    {if empty($error)}
        <p class="italic underline">
            <strong><a href="{{ $design->url('friend','main','index',$username) }}">{friend_number}</a></strong>
        </p>
        <br />

        {each $f in $friends}
            <div class="s_photo" id="friend_{% $f->fdId %}">
                {{ $avatarDesign->get($f->username, $f->firstName, $f->sex, 64, $bRollover = true) }}
                <em><abbr title="{% $f->firstName %}">{% $f->username %}</abbr></em><br />

                {if $is_user_auth AND $sess_member_id == $member_id}
                    {if $sess_member_id == $f->friendId AND $f->pending == FriendCoreModel::PENDING_REQUEST}
                        <small>{lang 'Pending...'}</small> <a href="javascript:void(0)" onclick="friend('approval',{% $f->fdId %},'{csrf_token}')">{lang 'Approve'}</a>
                    {/if}
                    <a href="javascript:void(0)" onclick="friend('delete',{% $f->fdId %},'{csrf_token}')">
                        {lang 'Delete'}
                    </a>
                {/if}
            </div>
        {/each}

        {main_include 'page_nav.inc.tpl'}
        <br />
        <p class="center bottom">
            <a class="btn btn-default btn-md" href="{{ $design->url('friend','main','search',"$username,$action") }}">
                {lang 'Search for a friend of %0%', $username}
            </a>
        </p>
    {else}
        <p>{error}</p>
    {/if}
</div>
