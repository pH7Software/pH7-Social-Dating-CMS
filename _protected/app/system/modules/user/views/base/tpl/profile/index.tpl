{@if(!empty($img_background))@}
  {* Sets The Profile Background *}
  <script>
  document.body.style.backgroundImage="url('{url_data_sys_mod}user/background/img/{username}/{img_background}')";
  document.body.style.backgroundRepeat='repeat';
  document.body.style.backgroundPosition='top center';
  </script>
{@/if@}

{@if(empty($error))@}

<ol id="toc">
    <li><a href="#general"><span>{@lang('Info')@}</span></a></li>
    <li><a href="#map"><span>{@lang('Map')@}</span></a></li>
    <li><a href="#friend"><span>{friend_link}</span></a></li>
    {@if(User::auth() && !$this->str->equals((int)$member_id, $id))@}<li><a href="#mutual_friend"><span>{mutual_friend_link}</span></a></li>{@/if@}
    <li><a href="#picture"><span>{@lang('Photos')@}</span></a></li>
    <li><a href="#video"><span>{@lang('Videos')@}</span></a></li>
    <li><a href="#forum"><span>{@lang('Topics')@}</span></a></li>
    <li><a href="#note"><span>{@lang('Notes')@}</span></a></li>
    <li><a href="#visitor"><span>{@lang('Recently Viewed')@}</span></a></li>
    <li><a rel="nofollow" href="{mail_link}"><span>{@lang('Send Message')@}</span></a></li>
    <li><a rel="nofollow" href="{messenger_link}"><span>{@lang('Live Chat')@}</span></a></li>
    <li><a ref="nofollow" href="{befriend_link}"><span>{@lang('Add Friend')@}</span></a></li>
    {@if(User::auth() && !$this->str->equals((int)$member_id, $id))@}<li><a href="{{ $design->url('love-calculator','main','index',$username) }}" title="{@lang('Love Calculator')@}"><span>{@lang('Match')@} <b class="pink2">&hearts;</b></span></a></li>{@/if@}
</ol>

<div class="content" id="general">
{{ UserDesignCoreModel::userStatus($id) }}
{{ $oAvatarDesign->lightBox($username, $first_name, $sex, 400) }}

<p><span class="bold">{@lang('I am:')@}</span> <span class="italic">{@lang($sex)@}</span></p>
<div class="break"></div>

{@if(!empty($match_sex))@}
  <p><span class="bold">{@lang('Seeking a:')@}</span> <span class="italic">{@lang($match_sex)@}</span></p>
  <div class="break"></div>
{@/if@}

<p><span class="bold">{@lang('First name:')@}</span> <span class="italic">{first_name}</span></p>
<div class="break"></div>

{@if(!empty($last_name))@}
  <p><span class="bold">{@lang('Last name:')@}</span> <span class="italic">{last_name}</span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($age))@}
  <p><span class="bold">{@lang('Age:')@}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&age='.$age) }}">{age}</a> <span class="gray">({birth_date})</span></span></p>
  <div class="break"></div>
{@/if@}

<p><span class="bold">{@lang('Country:')@}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code) }}">{country}</a></span> <img src="{{ $design->getSmallFlagIcon($country_code) }}" title="{country}" alt="{country}" /></p>
<div class="break"></div>

{@if(!empty($city))@}
  <p><span class="bold">{@lang('City / Town:')@}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&city='.$city) }}">{city}</a></span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($state))@}
  <p><span class="bold">{@lang('State:')@}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&state='.$state) }}">{state}</a></span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($zip_code))@}
  <p><span class="bold">{@lang('Postal Code (zip):')@}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&zip='.$zip_code) }}">{zip_code}</a></span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($website))@}
  <p>{{ $design->favicon($website) }} &nbsp; <span class="bold">{@lang('Site:')@}</span> <span class="italic">{{ $design->urlTag($website) }}</span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($social_network_site))@}
  <p>{{ $design->favicon($social_network_site) }} &nbsp; <span class="bold">{@lang('Social Network Profile:')@}</span> <span class="italic">{{ $design->urlTag($social_network_site) }}</span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($join_date))@}
  <p><span class="bold">{@lang('Join Date:')@}</span> <span class="italic">{join_date}</span></p>
  <div class="break"></div>
{@/if@}

{@if(!empty($last_activity))@}
  <p><span class="bold">{@lang('Last Activity:')@}</span> <span class="italic">{last_activity}</span></p>
  <div class="break"></div>
{@/if@}

<p><span class="bold">{@lang('Views:')@}</span> <span class="italic">{% Framework\Mvc\Model\StatisticModel::getView($id,'Members') %}</span></p>
<div class="break"></div>

{{ RatingDesignCore::voting($id,'Members') }}

{@if(!empty($description))@}
  <div class="profile_desc"><p class="bold">{@lang('Description:')@}</p> <div class="quote"><p class="italic">{description}</p></div></div>
  </div>
{@/if@}
</div>

<div class="content" id="map">
<p><span class="bold">{@lang('Profile Map:')@}</span>{map}</p>
</div>

<div class="content" id="friend">
 <script>
  var url_friend_block = '{{ $design->url('user','friend','index',$username) }}';
  $('#friend').load(url_friend_block + ' #friend_block');
 </script>
</div>

{@if(User::auth() && !$this->str->equals((int)$member_id, $id))@}
  <div class="content" id="mutual_friend">
    <script>
     var url_mutual_friend_block = '{{ $design->url('user','friend','mutual',$username) }}';
     $('#mutual_friend').load(url_mutual_friend_block + ' #friend_block');
    </script>
  </div>
{@/if@}

<div class="content" id="picture">
 <script>
  var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
  $('#picture').load(url_picture_block + ' #picture_block');
 </script>
</div>

<div class="content" id="video">
 <script>
  var url_video_block = '{{ $design->url('video','main','albums',$username) }}';
  $('#video').load(url_video_block + ' #video_block');
 </script>
</div>

<div class="content" id="forum">
 <script>
  var url_forum_block = '{{ $design->url('forum','forum','showpostbyprofile',$username) }}';
  $('#forum').load(url_forum_block + ' #forum_block');
 </script>
</div>

<div class="content" id="note">
 <script>
  var url_note_block = '{{ $design->url('note','main','author',$username) }}';
  $('#note').load(url_note_block + ' #note_block');
 </script>
</div>


<div class="content" id="visitor">
 <script>
  var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
  $('#visitor').load(url_visitor_block + ' #visitor_block');
 </script>
</div>

<p class="center">{{ $design->like($username, $first_name, $sex) }} | {{ $design->report($id, $username, $first_name, $sex) }}</p>
{{ $design->likeApi() }}

<p>----------------------------------------</p>
{{ CommentDesignCore::link($id, 'Profile') }}

<script src="{url_static_js}tabs.js"></script>
<script>tabs('p', ['general','map','friend',{@if(User::auth() && !$this->str->equals((int)$member_id, $id))@}'mutual_friend',{@/if@}'picture','video','forum','note','visitor']);</script>

{* Signup Popup *}
{@if(!User::auth() && !AdminCore::auth())@}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_JS, 'signup_popup.js') }}
{@/if@}

{@else@}

<p class="center">{error}</p>

{@/if@}
