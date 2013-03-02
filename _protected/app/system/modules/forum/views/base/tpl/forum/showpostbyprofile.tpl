 <div class="center" id="forum_block">

{@if(empty($error))@}

<p class="italic underline"><strong><a href="{{ $design->url('forum','forum','showpostbyprofile',$username) }}">{topic_number}</a></strong></p><br />

  {@foreach($topics as $topic)@}

    <p><a href="{{ $design->url('forum', 'forum', 'post', "$topic->name,$topic->forumId,$topic->title,$topic->topicId") }}">{% escape(Framework\Security\Ban\Ban::filterWord($topic->title), true) %}</a></p>
    <p>{% substr(escape(Framework\Security\Ban\Ban::filterWord($topic->message), true), 0, 100) %}</p>

  {@/foreach@}

  {@main_include('page_nav.inc.tpl')@}

  {@else@}

  <p>{error}</p>

{@/if@}

</div>
