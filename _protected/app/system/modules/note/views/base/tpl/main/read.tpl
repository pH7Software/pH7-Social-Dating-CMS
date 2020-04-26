<div class="center" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    {if empty($error)}
        <article>
            <time {if !empty($post->updatedDate)}itemprop="dateModified" datetime="{% $dateTime->get($post->updatedDate)->dateTime() %}{else}itemprop="datePublished" datetime="{% $dateTime->get($post->createdDate)->dateTime() %}" pubdate="pubdate"{/if}></time>

            {{ $content = Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($post->content)) }}

            <div class="left">
                <h4 itemprop="author">{lang 'Author: <a href="%0%" data-load="ajax">%1%</a>', (new UserCore)->getProfileSignupLink($post->username, $post->firstName, $post->sex), $post->firstName}</h4>
                {{ NoteDesign::thumb($post) }}
            </div>

            <div itemprop="articleBody">
                {content}<br />
            </div>

            <hr />
            {{ NoteDesign::categories($categories, 'note') }}

            {{ $design->like($post->username,$post->firstName,$post->sex) }} | {{ $design->report($post->profileId,$post->username,$post->firstName,$post->sex) }}

            <p class="small italic">
                {lang 'Posted on:'} {% $dateTime->get($post->createdDate)->dateTime() %} {if !empty($post->updatedDate)} | {lang 'Updated Post:'}{% $dateTime->get($post->updatedDate)->dateTime() %}{/if} | {lang 'Views:'} {% Framework\Mvc\Model\Statistic::getView($post->noteId,DbTableName::NOTE) %}
            </p>

            {if $is_user_auth AND $member_id === $post->profileId}
                <p>
                    <a class="btn btn-default btn-sm" href="{{ $design->url('note','main','edit',$post->noteId) }}">{lang 'Edit Article'}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'note', 'main', 'delete', $post->noteId, 'btn btn-default btn-sm') }}
                </p>
            {/if}

            {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('note','main','read',"$post->username,$post->postId")) }}
            {{ RatingDesignCore::voting($post->noteId,DbTableName::NOTE,'center') }}

            {{ $design->socialMediaWidgets() }}

            {if $post->enableComment}
                <p>------------------------------</p>
                {{ CommentDesignCore::link($post->noteId,'note') }}
            {/if}

            {if $is_admin_auth AND !UserCore::isAdminLoggedAs()}
                {{ $action = ($post->approved == 1) ? 'disapproved' : 'approved' }}
                {{ $text = ($post->approved == 1) ? t('Disapprove') : t('Approve') }}

                <fieldset>
                    <legend>{lang 'Moderation Action'}</legend>
                    <div>{{ LinkCoreForm::display($text, 'note', 'admin', $action, array('note_id'=>$post->noteId)) }} &nbsp; | &nbsp; <a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$post->profileId) }}" title="{lang 'Login as this author to edit/delete this post'}">{lang 'Login as this User'}</a></div>
                </fieldset>
            {/if}
        </article>
    {else}
        <p>{error}</p>
    {/if}
</div>
