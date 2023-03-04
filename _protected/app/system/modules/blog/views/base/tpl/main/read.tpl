<div itemscope="itemscope" itemtype="http://schema.org/NewsArticle">
    {if empty($error)}
        <article>
            <time {if !empty($updated_date)}itemprop="dateModified" datetime="{updated_date}"{else}itemprop="datePublished" datetime="{created_date}" pubdate="pubdate"{/if}></time>

            <div itemprop="articleBody" class="center s_bMarg">
                {content}
            </div>

            <div class="center">
              {{ BlogDesign::categories($categories, 'blog') }}

              <p class="small italic text-muted">
                  {lang 'Posted on %0%', $created_date} {if !empty($updated_date)} | {lang 'Last Edited: %0%', $updated_date}{/if} | {lang 'Views: %0%', Framework\Mvc\Model\Statistic::getView($blog_id,DbTableName::BLOG)}
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
            </div>
        </article>
    {else}
        <p>{error}</p>
    {/if}
</div>
