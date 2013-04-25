<div class="center">

{@if(empty($error))@}

 <article>
 <time datetime="{@if(!empty($updated_date))@} {updated_date} {@else@} {created_date} {@/if@}" pubdate="pubdate"></time>

 {content}

 <br /><br /><hr />
 <p>{@lang('Categories:')@}<span class="small italic">
 {@foreach($categories as $category)@}
   <a href="{{ $design->url('blog','main','category', "$category->name,title,asc") }}" data-load="ajax">{% $category->name %}</a> &bull;
 {@/foreach@}
 </span></p>

 <p class="small italic">{@lang('Posted on:')@} {created_date} {@if(!empty($updated_date))@} | {@lang('Updated Post:')@}{updated_date}{@/if@} | {@lang('Views:')@} {% Framework\Mvc\Model\Statistic::getView($blog_id,'Blogs') %}</p>

 {@if(AdminCore::auth())@}
  <p><a class="m_button" href="{{ $design->url('blog', 'admin', 'edit', $blog_id) }}">{@lang('Edit Article')@}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'blog', 'admin', 'delete', $blog_id, 'm_button') }}</p>
 {@/if@}

 {{ ShareUrlCoreForm::display(Framework\Mvc\Router\UriRoute::get('blog','main','read',$post_id)) }}
 {{ RatingDesignCore::voting($blog_id,'Blogs','center') }}

 {{ $design->likeApi() }}

 {@if($enable_comment)@}
    <p>------------------------------------</p>
    {{ CommentDesignCore::link($blog_id,'Blog') }}
 {@/if@}

 </article>

{@else@}

 <p>{error}</p>

{@/if@}

</div>
