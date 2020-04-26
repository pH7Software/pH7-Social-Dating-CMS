<div class="center" itemscope="itemscope" itemtype="http://schema.org/NewsArticle">
    {if empty($error)}
        <article>
            <time {if !empty($updated_date)}itemprop="dateModified" datetime="{updated_date}"{else}itemprop="datePublished" datetime="{created_date}" pubdate="pubdate"{/if}></time>

            <div itemprop="articleBody">
                {content}<br /><br />
            </div>

            <hr />
            {{ BlogDesign::categories($categories, 'blog') }}

            <p class="small italic">
                {lang 'Posted on:'} {created_date} {if !empty($updated_date)} | {lang 'Updated Post:'}{updated_date}{/if} | {lang 'Views:'} {% Framework\Mvc\Model\Statistic::getView($blog_id,DbTableName::BLOG) %}
            </p>
            {if AdminCore::auth()}
                <p>
                    <a class="btn btn-default btn-sm" href="{{ $design->url('blog', 'admin', 'edit', $blog_id) }}">{lang 'Edit Article'}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'blog', 'admin', 'delete', $blog_id, 'btn btn-default btn-sm') }}
                </p>
            {/if}

            {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('blog','main','read',$post_id)) }}
            {{ RatingDesignCore::voting($blog_id,DbTableName::BLOG,'center') }}

            {{ $design->socialMediaWidgets() }}

            {if $enable_comment}
                <p>------------------------------</p>
                {{ CommentDesignCore::link($blog_id,'blog') }}
            {/if}
        </article>
    {else}
        <p>{error}</p>
    {/if}
</div>
