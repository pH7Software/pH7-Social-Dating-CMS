{if empty($error)}
    <div class="middle">
        <form method="post" action="{{ $design->url('mail','main','inbox') }}">
            {{ $designSecurity->inputToken('mail_action') }}

            {* Set Variables *}
            {{ $is_admin = ($is_admin_auth && !$is_user_auth) }}
            {{ $ctrl = ($is_admin) ? 'admin' : 'main' }}

            {if $is_admin}<div class="divShow">{/if}

            {each $msg in $msgs}
                {* Set Variables *}
                {{ $username_sender = (empty($msg->username)) ? PH7_ADMIN_USERNAME : $msg->username }}
                {{ $firstName_sender = (empty($msg->firstName)) ? PH7_ADMIN_USERNAME : $msg->firstName }}
                {{ $subject = escape(substr(Framework\Security\Ban\Ban::filterWord($msg->title, false),0,20), true) }}
                {{ $message = escape(Framework\Security\Ban\Ban::filterWord($msg->message), true) }}
                {{ $is_outbox = ($msg->sender == $member_id) }}
                {{ $is_trash = (($msg->sender == $member_id && $msg->trash == 'sender') || ($msg->recipient == $member_id && $msg->trash == 'recipient') && !$is_outbox && !$is_admin) }}
                {{ $slug_url = ($is_trash ? 'trash' : ($is_outbox ? 'outbox' : 'inbox')) }}
                {{ $is_delete = ($is_outbox || $is_trash || $is_admin) }}
                {{ $move_to = ($is_delete) ? 'delete' : 'trash' }}
                {{ $label_txt = ($is_delete) ? t('Delete') : t('Trash') }}

                <div class="msg_content" id="mail_{% $msg->messageId %}">
                    <div class="left">
                        <input type="checkbox" name="action[]" value="{% $msg->messageId %}" />
                    </div>

                    {if $msg->status == MailModel::UNREAD_STATUS}
                        <span class="label label-primary">{lang 'New'}</span>
                    {/if}

                    <div class="user">{{ $avatarDesign->get($username_sender, $firstName_sender, null, 32) }}</div>

                    {if $is_admin}
                        <div class="content" title="{lang 'See more'}"><a href="#divShow_{% $msg->messageId %}">
                    {else}
                        <div class="content" title="{lang 'See more'}" onclick="location.href='{{ $design->url('mail','main',$slug_url,$msg->messageId) }}'">
                    {/if}

                    <div class="subject">{subject}</div>
                    <div class="message">{% substr($message,0,50) %}</div>

                    {if $is_admin}
                        </a>
                    {/if}

                    </div>
                    <div class="date italic small">{% Framework\Date\Various::textTimeStamp($msg->sendDate) %}</div>

                    {if $is_admin}
                        {*  Hide the message *}
                        <div class="hidden center" id="divShow_{% $msg->messageId %}">{message}</div>
                    {/if}

                    <div class="action">
                        <a href="{{ $design->url('mail','main','compose',"$username_sender,$subject") }}">{lang 'Reply'}</a> | <a href="javascript:void(0)" onclick="mail('{move_to}',{% $msg->messageId %},'{csrf_token}')">{label_txt}</a>
                        {if $is_trash}
                            | <a href="javascript:void(0)" onclick="mail('restore',{% $msg->messageId %},'{csrf_token}')">{lang 'Restore'}</a>
                        {/if}
                    </div>
                </div>
            {/each}

            {if $is_admin}
                </div>
            {/if}

            <p>
                <input type="checkbox" name="all_action" />
                <button
                    class="btn btn-default btn-md"
                    type="submit"
                    onclick="return checkChecked()"
                    formaction="{{ $design->url('mail',$ctrl,'set'.$move_to.'all') }}"
                    >{label_txt}
                </button>
                {if $is_trash}
                    | <button class="btn btn-default btn-md" type="submit" onclick="return checkChecked(false)" formaction="{{ $design->url('mail',$ctrl,'setrestoreall') }}">{lang 'Move to Inbox'}</button>
                {/if}
            </p>
        </form>
    </div>
    {main_include 'page_nav.inc.tpl'}
{else}
    <p class="center bold">{error}</p>
{/if}
