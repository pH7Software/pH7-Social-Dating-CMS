<div class="center">
    {if empty($error)}
        <article>
            <time datetime="{if !empty($updated_date)} {updated_date} {else} {created_date} {/if}" pubdate="pubdate"></time>
            {content}
            <br /><br /><hr />
            {{ BlogDesign::categories($categories, 'blog') }}

            <p class="small italic">
                {lang 'Posted on:'} {created_date} {if !empty($updated_date)} | {lang 'Updated Post:'}{updated_date}{/if} | {lang 'Views:'} {% Framework\Mvc\Model\Statistic::getView($blog_id,'Blogs') %}
            </p>
            {if AdminCore::auth()}
                <p>
                    <a class="btn btn-default btn-sm" href="{{ $design->url('blog', 'admin', 'edit', $blog_id) }}">{lang 'Edit Article'}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'blog', 'admin', 'delete', $blog_id, 'btn btn-default btn-sm') }}
                </p>
            {/if}

            {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('blog','main','read',$post_id)) }}
            {{ RatingDesignCore::voting($blog_id,'Blogs','center') }}

            {{ $design->likeApi() }}

            {if $enable_comment}
                <p>------------------------------------</p>
                {{ CommentDesignCore::link($blog_id,'Blog') }}
            {/if}
        </article>
    {else}
        <p>{error}</p>
    {/if}
</div>
