<div class="center">

{@if(empty($error))@}

 {@foreach($forums as $forum)@}

     {@if($forum->categoryId == $forum->forumId)@}
        <h2>{% $forum->title %}</h2>

          {@if(AdminCore::auth())@}
            <a class="m_button" href="{{ $design->url('forum', 'admin', 'editcategory', $forum->categoryId) }}">{@lang('Edit Category')@}</a> | {{ $design->popupLinkConfirm(t('Delete Category'), 'forum', 'admin', 'deletecategory', $forum->categoryId, 'm_button') }}
          {@/if@}

     {@/if@}

        <p><a href="{{ $design->url('forum', 'forum', 'topic', "$forum->name,$forum->forumId") }}">{% escape($forum->name, true) %}</a></p>
        <p>{% escape($forum->description, true) %}</p>

          {@if(AdminCore::auth())@}
            <a class="m_button" href="{{ $design->url('forum', 'admin', 'editforum', $forum->forumId) }}">{@lang('Edit Forum')@}</a> | {{ $design->popupLinkConfirm(t('Delete Forum'), 'forum', 'admin', 'deleteforum', $forum->forumId, 'm_button') }}
          {@/if@}

 {@/foreach@}

  {@main_include('page_nav.inc.tpl')@}

{@else@}

  <p>{error}</p>

{@/if@}

{@if(AdminCore::auth())@}
  <br /><hr /><p><a class="m_button" href="{{ $design->url('forum', 'admin', 'addcategory') }}">{@lang('Add Category')@}</a> | <a class="m_button" href="{{ $design->url('forum', 'admin', 'addforum') }}">{@lang('Add Forum')@}</a></p>
{@/if@}

</div>
