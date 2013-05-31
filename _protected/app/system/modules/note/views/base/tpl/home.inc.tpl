<div class="box-left">

  <div class="design-box">
  <h2>{@lang('Search Note Posts')@}</h2>
  {{ SearchNoteForm::display(168) }}
  </div>

  <div class="design-box">
    <h2>{@lang('Top Authors')@}</h2>
    <ul>
    {@foreach($authors as $author)@}
      <li><a href="{{ $design->url('note','main','author',$author->username) }}" title="{% $author->username %}">{% substr($author->username,0,20) %}</a> - ({% $author->totalAuthors %})</li>
    {@/foreach@}
    </ul>
  </div>

  <div class="design-box">
    <h2>{@lang('Categories')@}</h2>
    <ul>
    {@foreach($categories as $category)@}
      <li><a href="{{ $design->url('note','main','category',$category->name) }}" title="{% $category->name %}">{% $category->name %}</a> - ({% $category->totalCatNotes %})</li>
    {@/foreach@}
    </ul>
  </div>

  <div class="design-box">
    <h2>{@lang('Top Popular Posts')@}</h2>
    <ul>
    {@foreach($top_views as $views)@}
      <li><a href="{{ $design->url('note','main','read',"$views->username,$views->postId") }}" title="{% $views->pageTitle %}">{% $views->title %}</a></li>
    {@/foreach@}
    </ul>
  </div>

  <div class="design-box">
    <h2>{@lang('Top Rated Posts')@}</h2>
    <ul>
    {@foreach($top_rating as $rating)@}
      <li><a href="{{ $design->url('note','main','read',"$rating->username,$rating->postId") }}" title="{% $rating->pageTitle %}">{% $rating->title %}</a></li>
    {@/foreach@}
    </ul>
  </div>

</div>

<div class="box-right">

<div class="center" id="note_block">

{@if(!empty($error))@}

<p>{error}</p>

{@else@}

{@foreach($posts as $post)@}

{{ $content = escape($this->str->extract(Framework\Security\Ban\Ban::filterWord($post->content),0,400), true) }}

<h1><a href="{{$design->url('note','main','read',"$post->username,$post->postId")}}" title="{% $post->title %}">{% escape(Framework\Security\Ban\Ban::filterWord($post->title)) %}</a></h1>
<div class="left">{{ NoteDesign::thumb($post) }}</div>
{content}
<p><a href="{{$design->url('note','main','read',"$post->username,$post->postId")}}">{@lang('See more')@}</a></p>

{@if(UserCore::auth() && $member_id === $post->profileId)@}
  <p><a class="s_button" href="{{ $design->url('note','main','edit',$post->noteId) }}">{@lang('Edit Article')@}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'note','main','delete', $post->noteId, 's_button') }}</p>
{@/if@}

{@if(AdminCore::auth() && !(new Framework\Session\Session)->exists('login_user_as'))@}
  {{ $action = ($post->approved == 1) ? 'disapproved' : 'approved' }}
  {{ $text = ($post->approved == 1) ? t('Disapprove') : t('Approve') }}
  <p><hr />{{ LinkCoreForm::display($text, 'note', 'admin', $action, array('note_id'=>$post->noteId, 'post_id'=>$post->postId, 'profile_id'=>$post->profileId)) }} | <a class="m_button" href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$post->profileId) }}" title="{@lang('Login as this author to edit his post. Please first approve this note as an administrator to be able to edit or delete it.')@}">{@lang('Login as this User')@}</a></p>
{@/if@}

{{ $design->likeApi() }}

<hr /><br />

{@/foreach@}

{@main_include('page_nav.inc.tpl')@}

{@/if@}
</div>

<div class="center">
<p><a class="m_button" href="{{ $design->url('note','main','add') }}">{@lang('Add a new Article')@}</a></p>
<p><a class="m_button" href="{{$design->url('note','main','search')}}">{@lang('Search a Note')@}</a></p>
<p><a href="{{$design->url('xml','rss','xmlrouter','note')}}"><img src="{url_static_img}icon/feed.png" alt="RSS Feed" /></a></p>
</div>

</div>
