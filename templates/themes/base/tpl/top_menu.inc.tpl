    {* Get the frequently used functions in variables to optimize the script and call these functions only once in the file *}
    {{
      $admin_logged_as_user = UserCore::isAdminLoggedAs();
      $admin_logged_as_affiliate = AffiliateCore::isAdminLoggedAs();
    }}

    {{
      $oSession = new Framework\Session\Session();
      $username = $oSession->get('member_username');
      unset($oSession);
    }}


    {* Menu for All *}
      {if $top_navbar_type === 'inverse'}
        <nav class="navbar navbar-inverse navbar-fixed-top">
      {else}
        <nav class="navbar navbar-default navbar-fixed-top">
      {/if}
        <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div role="banner" id="logo" class="navbar-brand">
              <h1>
                <a href="{{ $design->homePageUrl() }}" title="{slogan}">
                  {site_name}
                </a>
              </h1>
            </div>
          </div>

          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

    {* Guest Menu *}
      {if !$is_user_auth AND !$is_aff_auth AND !$is_admin_auth}
        <li>
          <a class="bold" href="{{ $design->url('user', 'signup', 'step1') }}" title="{lang 'Join Now!'}">
            <i class="fa fa-user-plus"></i> {lang 'Join Now!'}
          </a>
        </li>
        <li>
          <a href="{{ $design->url('user', 'main', 'login') }}" title="{lang 'Login'}" data-load="ajax">
            <i class="fa fa-sign-in"></i> {lang 'Login'}
          </a>
        </li>
      {/if}


    {* Menu Guest, Member and Admin *}
      {if !$is_aff_auth}
        <li class="dropdown">
          <a href="{{ $design->url('user', 'browse', 'index') }}" title="{lang 'Members'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax">
            <i class="fa fa-users fa-fw"></i> {lang 'People'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url('user', 'browse', 'index') }}" rel="nofollow" title="{lang 'Browse Members'}" data-load="ajax"><i class="fa fa-user"></i> {lang 'Browse'}</a></li>

            <li class="menu-item dropdown dropdown-submenu">
              <a href="{{ $design->url('user','search', 'index') }}" title="{lang 'Search the members'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax">
                <i class="fa fa-search"></i> {lang 'Search'}
              </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('user', 'search', 'quick') }}" title="{lang 'Quick Search'}" data-load="ajax">{lang 'Quick Search'}</a></li>
                <li><a href="{{ $design->url('user', 'search', 'advanced') }}" title="{lang 'Advanced Search'}" data-load="ajax">{lang 'Advanced Search'}</a></li>
              </ul>
            </li>

            {if $is_map_enabled}
              <li>
                <a href="{{ $design->url('map', 'country', 'index', Framework\Geo\Ip\Geo::getCountry() . PH7_SH. Framework\Geo\Ip\Geo::getCity()) }}" title="{lang 'Users nearby through the map!'}"><i class="fa fa-map-marker"></i> {lang 'People Nearby'}</a>
              </li>
            {/if}

            {if $is_birthday_enabled}
              <li class="menu-item dropdown dropdown-submenu">
                <a href="{{ $design->url('birthday', 'user', 'index') }}" title="{lang 'User Birthdays'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax">
                  <i class="fa fa-birthday-cake"></i> {lang 'Birthdays'}
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('birthday', 'user', 'index', 'all') }}" rel="nofollow" title="{lang 'All Birthdays'}" data-load="ajax">{lang 'All Birthdays'}</a></li>
                  <li><a href="{{ $design->url('birthday', 'user', 'index', 'male') }}" title="{lang 'Men Birthdays'}" data-load="ajax">{lang 'Men Birthdays'}</a></li>
                  <li><a href="{{ $design->url('birthday', 'user', 'index', 'female') }}" title="{lang 'Women Birthdays'}" data-load="ajax">{lang 'Women Birthdays'}</a></li>
                  <li><a href="{{ $design->url('birthday', 'user', 'index', 'couple') }}" title="{lang 'Couple Birthdays'}" data-load="ajax">{lang 'Couple Birthdays'}</a></li>
                </ul>
              </li>
            {/if}
          </ul>
        </li>
      {/if}


    {* Menu Guest, Member and LoginUserAs of Admin Panel *}
      {if (!$is_aff_auth AND !$is_admin_auth) OR $admin_logged_as_user }
        {if $is_chat_enabled OR $is_chatroulette_enabled}
          <li class="dropdown"><a href="#" title="{lang 'Free Social Dating Chat Rooms'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-weixin"></i> {lang 'Chat'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              {if $is_chat_enabled}
                <li><a href="{{ $design->url('chat','home','index') }}" rel="nofollow" title="{lang 'Chat Rooms'}" data-load="ajax"><i class="fa fa-weixin"></i> {lang 'Chat'}</a></li>
              {/if}

              {if $is_chatroulette_enabled}
                <li><a href="{{ $design->url('chatroulette','home','index') }}" title="{lang 'Chat Roulette'}"><i class="fa fa-random"></i> {lang 'Chatroulette'}</a></li>
              {/if}

            </ul>
          </li>
        {/if}

        {if $is_picture_enabled}
          <li class="dropdown">
            <a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax">
              <i class="fa fa-picture-o"></i> {lang 'Photo'} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('picture','main','index') }}" rel="nofollow" title="{lang 'Photo Gallery'}" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Photos'}</a></li>

              {if $is_hotornot_enabled}
                <li><a href="{{ $design->url('hotornot','main','rating') }}" title="{lang 'Hot Or Not'}" data-load="ajax"><i class="fa fa-heart-o"></i> {lang 'Hot Or Not'}</a></li>
              {/if}

              <li><a href="{{ $design->url('picture','main','search') }}" title="{lang 'Search Photos'}" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
            </ul>
          </li>
        {/if}

        {if $is_video_enabled}
          <li class="dropdown"><a href="{{ $design->url('video','main','index') }}" title="{lang 'Video Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-youtube-play"></i> {lang 'Video'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('video','main','index') }}" rel="nofollow" title="{lang 'Video Gallery'}" data-load="ajax"><i class="fa fa-youtube-play"></i> {lang 'Videos'}</a></li>
              <li><a href="{{ $design->url('video','main','search') }}" title="{lang 'Search Videos'}" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
            </ul>
          </li>
        {/if}

        {if $is_game_enabled}
          <li class="dropdown"><a href="{{ $design->url('game','main','index') }}" title="{lang 'Games Zone'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-gamepad"></i> {lang 'Game'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('game','main','index') }}" rel="nofollow" title="{lang 'Games Zone'}" data-load="ajax"><i class="fa fa-gamepad"></i> {lang 'Game'}</a></li>
              <li><a href="{{ $design->url('game','main','search') }}" title="{lang 'Search Games'}" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
            </ul>
          </li>
        {/if}

        {if $is_forum_enabled}
          <li class="dropdown"><a href="{{ $design->url('forum','forum','index') }}" title="{lang 'Forums'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-comments"></i> {lang 'Forum'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('forum','forum','index') }}" rel="nofollow" title="{lang 'Forums'}" data-load="ajax"><i class="fa fa-comments"></i> {lang 'Forum'}</a></li>
              <li><a href="{{ $design->url('forum','forum','search') }}" title="{lang 'Search Topics'}" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
            </ul>
          </li>
        {/if}

        {if $is_note_enabled}
          <li class="dropdown"><a href="{{ $design->url('note','main','index') }}" title="{lang 'Community Notes'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-newspaper-o"></i> {lang 'Note'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('note','main','index') }}" rel="nofollow" title="{lang 'Community Notes'}" data-load="ajax"><i class="fa fa-newspaper-o"></i> {lang 'Notes'}</a></li>
              <li><a href="{{ $design->url('note','main','search') }}" title="{lang 'Search Notes'}" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
            </ul>
          </li>
        {/if}
      {/if}


    {* Member Menu *}
      {if $is_user_auth AND ( !$is_aff_auth AND !$is_admin_auth ) OR $admin_logged_as_user }
          {if $is_mail_enabled}
            <li class="dropdown">
              <a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'My Messages'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                <i class="fa fa-envelope-o fa-fw"></i> {lang 'Messages'} {if $count_unread_mail}<span class="badge">{count_unread_mail}</span>{/if} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('mail','main','compose') }}" title="{lang 'Compose'}"><i class="fa fa-pencil"></i> {lang 'Compose'}</a></li>
                <li><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'Inbox'}"><i class="fa fa-inbox"></i> {lang 'Inbox'}</a></li>
                <li><a href="{{ $design->url('mail','main','outbox') }}" title="{lang 'Sent'}"><i class="fa fa-paper-plane-o"></i> {lang 'Sent'}</a></li>
                <li><a href="{{ $design->url('mail','main','trash') }}" title="{lang 'Trash'}"><i class="fa fa-trash-o"></i> {lang 'Trash'}</a></li>
                <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Search'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
              </ul>
            </li>
          {/if}

          <noscript>
            <li class="dropdown">
              <a href="{{ $design->url('user','setting','edit') }}" title="{lang 'Settings'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                {lang 'Settings'} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('user','setting','edit') }}">{lang 'Edit Profile'}</a></li>
                <li><a href="{{ $design->url('user','setting','design') }}">{lang 'Design Profile'}</a></li>
                <li><a href="{{ $design->url('user','setting','notification') }}">{lang 'Notifications'}</a></li>
                <li><a href="{{ $design->url('user','setting','privacy') }}">{lang 'Privacy Settings'}</a></li>
                <li><a href="{{ $design->url('payment','main','info') }}">{lang 'Membership Details'}</a></li>
                <li><a href="{{ $design->url('user','setting','password') }}">{lang 'Change Password'}</a></li>
              </ul>
            </li>
          </noscript>

          <li class="dropdown">
            <a href="{{ $design->url('user','account','index') }}" title="{lang 'My Account'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
              <i class="fa fa-cog"></i> {lang 'Account'} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('user','setting','index') }}" title="{lang 'My Settings'}"><i class="fa fa-cog fa-fw"></i> {lang 'Edit Profile'}</a></li>
              <li><a href="{% (new UserCore)->getProfileLink($username) %}" title="{lang 'See My Profile'}"><i class="fa fa-user fa-fw"></i> {lang 'See My Profile'}</a></li>
              <li><a href="{{ $design->url('user','setting','avatar') }}" title="{lang 'Change Profile Photo'}"><i class="fa fa-upload"></i> {lang 'Profile Photo'}</a></li>

              {if $is_picture_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Photo Gallery'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('picture','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                    <li><a href="{{ $design->url('picture','main','albums',$username) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                  </ul>
                </li>
              {/if}

              {if $is_video_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('video','main','index') }}" title="{lang 'Videos Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-youtube-play"></i> {lang 'Videos Gallery'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('video','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                    <li><a href="{{ $design->url('video','main','albums',$username) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                  </ul>
                </li>
              {/if}

              {if $is_note_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('note','main','index') }}" title="{lang 'Notes'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Note'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('note','main','add') }}" title="{lang 'Add a Note'}">{lang 'Add a Note'}</a></li>
                    <li><a href="{{ $design->url('note','main','author',$username) }}" title="{lang 'My Notes'}">{lang 'My Notes'}</a></li>
                  </ul>
                </li>
              {/if}

              {if $is_friend_enabled}
                  <li class="menu-item dropdown dropdown-submenu">
                    <a href="{{ $design->url('friend','main','index') }}" title="{lang 'Friends Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                      <i class="fa fa-users"></i> {lang 'Friends Manager'} {if $count_pen_friend_request}<span class="badge">{count_pen_friend_request}</span>{/if}
                      </a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="{{ $design->url('friend','main','index') }}" title="{lang 'Friends List'}">{lang 'Friends List'}</a></li>
                    <li><a href="{{ $design->url('friend','main','search',$username) }}" title="{lang 'Find a friend from my list'}">{lang 'Find a Friend'}</a></li>
                  </ul>
                </li>
              {/if}

              <li class="menu-item dropdown dropdown-submenu">
                <a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                  <i class="fa fa-eye"></i> {lang 'Who See Me'}
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}">{lang 'Who See Me'}</a></li>
                  <li><a href="{{ $design->url('user','visitor','search',$username) }}" title="{lang 'See who visited my profile'}">{lang 'Find Visitor(s)'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('user','main','logout') }}" title="{lang 'Logout'}"><i class="fa fa-sign-out"></i> {lang 'Logout'}</a></li>
            </ul>
          </li>
      {/if}


    {* Affiliate Menu *}
      {if $is_affiliate_enabled AND $is_aff_auth AND ( !$is_user_auth AND !$is_admin_auth OR $admin_logged_as_affiliate ) }
        <li><a href="{{ $design->url('affiliate','ads','index') }}" title="{lang 'Get Ad Banners'}"><i class="fa fa-money"></i> {lang 'Banners'}</a></li>

        <li class="dropdown"><a href="{{ $design->url('affiliate','account','index') }}" title="{lang 'My Account'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-cog"></i> {lang 'Account'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url('affiliate','account','index') }}" title="{lang 'My Account'}"><i class="fa fa-tachometer"></i> {lang 'Dashboard'}</a></li>
            <li><a href="{{ $design->url('affiliate','account','edit') }}" title="{lang 'Edit My Account'}"><i class="fa fa-cog"></i> {lang 'Edit My Account'}</a></li>
            <li><a href="{{ $design->url('affiliate','account','password') }}" title="{lang 'Change Password'}"><i class="fa fa-key fa-fw"></i> {lang 'Change Password'}</a></li>
            <li><a href="{{ $design->url('affiliate','home','logout') }}" title="{lang 'Logout'}"><i class="fa fa-sign-out"></i> {lang 'Logout'}</a></li>
          </ul>
        </li>
      {/if}


    {* Admin Menu *}
      {if $is_admin_auth AND ( !$is_user_auth AND !$is_aff_auth ) }
        {{ $count_total_reports = ReportCoreModel::totalReports() }}
        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'user','index') }}" title="{lang 'Users/Admins Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-user fa-fw"></i> {lang 'User/Admin'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Users Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user"></i> {lang 'Users'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Browse Users'}"><i class="fa fa-users"></i> {lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','search') }}" title="{lang 'Search Users'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','add') }}" title="{lang 'Add a User'}"><i class="fa fa-user-plus"></i> {lang 'Add User'}</a></li>
                <li><a href="{{ $design->url('report','admin','index') }}" title="{lang 'Report Abuse'}"><i class="fa fa-flag"></i> {lang 'Reports'} {if $count_total_reports}<span class="badge">{count_total_reports}</span>{/if}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','addfakeprofiles') }}" title="{lang 'Fake Profiles Automatic Generator (with profile photos)'}"><i class="fa fa-user-plus"></i> {lang 'Fake Profile Generator'}</a></li>
                <li><a href="{{ $design->url('profile-faker','generator','addmember') }}" title="{lang 'Generate Bulk Members (without profile photos)'}"><i class="fa fa-users fa-fw"></i> {lang 'Generate Bulk Members'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','import') }}" title="{lang 'Import Users'}"><i class="fa fa-user-plus"></i> {lang 'Import Users'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','countryrestriction') }}" title="{lang 'Country Restrictions for Registration and Search form'}"><i class="fa fa-globe"></i> {lang 'Country Restriction'}</a></li>
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'User Fields'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-check-circle-o"></i> {lang 'User Fields'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'Fields List'}">{lang 'Fields List'}</a></li>
                    <li><a href="{{ $design->url('field','field','add','user') }}" title="{lang 'Add Fields'}">{lang 'Add Fields'}</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Admins Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user"></i> {lang 'Admins'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Browse Admins'}"><i class="fa fa-users"></i> {lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','search') }}" title="{lang 'Search an Admin'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','add') }}" title="{lang 'Add an Admin'}"><i class="fa fa-user-plus"></i> {lang 'Add Admin'}</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'Settings'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-cog fa-fw"></i> {lang 'Setting'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'General Settings'}"><i class="fa fa-tachometer"></i> {lang 'General'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','metamain') }}" title="{lang 'Meta Tags/Homepage Texts'}"><i class="fa fa-tag"></i> {lang 'Meta Tags/Homepage Texts'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','ads') }}" title="{lang 'Add Banners on the best click-through-rate locations'}"><i class="fa fa-money"></i> {lang 'Ad Banners'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','analyticsapi') }}" title="{lang 'Analytics Code'}"><i class="fa fa-bar-chart"></i> {lang 'Analytics Code'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'style') }}" title="{lang 'Custom CSS Style'}"><i class="fa fa-code"></i> {lang 'Custom CSS'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'script') }}" title="{lang 'JavaScript Injection'}"><i class="fa fa-code"></i> {lang 'Custom JavaScript'}</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'module','disable') }}" title="{lang 'Modules Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-puzzle-piece"></i> {lang 'Mod'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','disable') }}" title="{lang 'Enable/Disable System Modules'}"><i class="fa fa-toggle-on"></i> {lang 'Enable/Disable Modules'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','index') }}" title="{lang 'Third-party Modules Manager'}"><i class="fa fa-plug"></i> {lang '3rd-party Mods Manager'}</a></li>

            {if $is_newsletter_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Newsletters'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}">{lang 'Send Newsletters'}</a></li>
                  <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">{lang 'Subscribers'}</a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}">{lang 'Browse'}</a></li>
                      <li><a href="{{ $design->url('newsletter', 'admin', 'search') }}" title="{lang 'Search Subscribers'}">{lang 'Search'}</a></li>
                    </ul>
                  </li>
                  <li><a href="{{ $design->url('profile-faker', 'generator', 'addsubscriber') }}" title="{lang 'Generate Bulk Subscribers'}">{lang 'Generate Bulk Subscribers'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_forum_enabled}
              <li><a href="{{ $design->url('forum','admin','index') }}" title="{lang 'Forum - Admin Mode'}"><i class="fa fa-comments"></i> {lang 'Forum'}</a></li>
            {/if}

            {if $is_blog_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('blog','admin','index') }}" title="{lang 'Admin Blog'}"><i class="fa fa-commenting-o"></i> {lang 'Blog'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('blog','admin','index') }}" title="{lang 'Admin Control - Blog'}">{lang 'Admin Blog'}</a></li>
                  <li><a href="{{ $design->url('blog','admin','add') }}" title="{lang 'Add a Blog Post'}">{lang 'Add a Post'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_note_enabled}
              <li><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Note Posts'}"><i class="fa fa-newspaper-o"></i> {lang 'Note'}</a></li>
            {/if}

            {if $is_game_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('game','admin','index') }}" title="{lang 'Admin Game'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-gamepad"></i> {lang 'Game'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('game','admin','index') }}" title="{lang 'Admin Game'}">{lang 'Admin Game'}</a></li>
                  <li><a href="{{ $design->url('game','admin','add') }}" title="{lang 'Add a Game'}">{lang 'Add a Game'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_affiliate_enabled}
              <li class="menu-item dropdown dropdown-submenu">
                <a href="{{ $design->url('affiliate','admin','index') }}" title="{lang 'Affiliate Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                  <i class="fa fa-money"></i> {lang 'Affiliate'}
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('affiliate','admin','banner') }}" title="{lang 'Banners Manager'}">{lang 'Banners'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','browse') }}" title="{lang 'Affiliates List'}">{lang 'Browse Affiliates'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','search') }}" title="{lang 'Search an Affiliate'}">{lang 'Search an Affiliate'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','add') }}" title="{lang 'Add an Affiliate'}">{lang 'Add Affiliate'}</a></li>
                  <li><a href="{{ $design->url('profile-faker','generator','addaffiliate') }}" title="{lang 'Generate Bulk Affiliates'}">{lang 'Generate Bulk Affiliates'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','config') }}" title="{lang 'Affiliate Settings'}">{lang 'Settings'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','countryrestriction') }}" title="{lang 'Country Restrictions for Registration form'}">{lang 'Country Restriction'}</a></li>
                  <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('field','field','all','aff') }}" title="{lang 'Affiliate Fields'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">{lang 'Affiliate Fields'}</a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="{{ $design->url('field','field','all','aff') }}" title="{lang 'Fields List'}">{lang 'Fields List'}</a></li>
                      <li><a href="{{ $design->url('field','field','add','aff') }}" title="{lang 'Add Fields'}">{lang 'Add Fields'}</a></li>
                    </ul>
                  </li>
                </ul>
              </li>
            {/if}

            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('payment','admin','index') }}" title="{lang 'Payment System'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-credit-card"></i> {lang 'Billing'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','general') }}#p=registration" title="{lang 'Set the Default Membership Group for new Users'}">{lang 'Default Membership Group'}</a></li>
                <li><a href="{{ $design->url('payment','admin','membershiplist') }}" title="{lang 'Membership Manager'}">{lang 'Memberships List'}</a></li>
                <li><a href="{{ $design->url('payment','admin','addmembership') }}" title="{lang 'Add a new Membership'}">{lang 'Add Membership'}</a></li>
                <li><a href="{{ $design->url('payment','admin','config') }}" title="{lang 'Payment Gateways Settings'}">{lang 'Gateways Configuration'}</a></li>
              </ul>
            </li>

            {if $is_mail_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('mail','admin','index') }}" title="{lang 'Member Mails Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-envelope-o"></i> {lang 'Mail Manager'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('mail','admin','msglist') }}" title="{lang 'Member Messages Monitor'}">{lang 'Messages Monitor'}</a></li>
                  <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Search Member Messages'}">{lang 'Search Messages'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_video_enabled}
              <li><a href="{{ $design->url('video', 'admin', 'config') }}"><i class="fa fa-youtube-play"></i> {lang 'Video Youtube API key'}</a></li>
            {/if}

            {if $is_smsverification_enabled}
              <li><a href="{{ $design->url('sms-verification', 'admin', 'config') }}"><i class="fa-user-check"></i> {lang 'SMS Gateway Verification APIs'}</a></li>
            {/if}

            {if $is_connect_enabled}
              <li><a href="{{ $design->url('connect', 'admin', 'config') }}"><i class="fa fa-share-alt-square"></i> {lang 'Universal Login Setting'}</a></li>
            {/if}

          </ul>
        </li>

        {* Moderate Count *}
          {{
            $oModeratorModel = new ModeratorCoreModel();

            $count_moderate_total_picture_album = $oModeratorModel->totalPictureAlbums();
            $count_moderate_total_picture = $oModeratorModel->totalPictures();
            $count_moderate_total_video_album = $oModeratorModel->totalVideoAlbums();
            $count_moderate_total_video = $oModeratorModel->totalVideos();
            $count_moderate_total_avatar = $oModeratorModel->totalAvatars();
            $count_moderate_total_background = $oModeratorModel->totalBackgrounds();
            $count_moderate_total_note = $oModeratorModel->totalNotes();

            unset($oModeratorModel);
          }}

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','index') }}" title="{lang 'User Moderation'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-user-secret"></i> {lang 'Moderation'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picturealbum') }}" title="{lang 'Moderate Photo Albums'}"><i class="fa fa-picture-o"></i> {lang 'Photo Albums'} {if $count_moderate_total_picture_album }<span class="badge">{count_moderate_total_picture_album}</span>{/if}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picture') }}" title="{lang 'Moderate Pictures'}"><i class="fa fa-picture-o"></i> {lang 'Photos'} {if $count_moderate_total_picture }<span class="badge">{count_moderate_total_picture}</span>{/if}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','videoalbum') }}" title="{lang 'Moderate Video Albums'}"><i class="fa fa-youtube-play"></i> {lang 'Video Albums'} {if $count_moderate_total_video_album }<span class="badge">{count_moderate_total_video_album}</span>{/if}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','video') }}" title="{lang 'Moderate Videos'}"><i class="fa fa-youtube-play"></i> {lang 'Videos'} {if $count_moderate_total_video }<span class="badge">{count_moderate_total_video}</span>{/if}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','avatar') }}" title="{lang 'Moderate Profile Photos'}"><i class="fa fa-picture-o"></i> {lang 'Profile Photos'} {if $count_moderate_total_avatar }<span class="badge">{count_moderate_total_avatar}</span>{/if}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','background') }}" title="{lang 'Moderate Profile Background'}"><i class="fa fa-picture-o"></i> {lang 'Profile Backgrounds'} {if $count_moderate_total_background}<span class="badge">{count_moderate_total_background}</span>{/if}</a></li>

            {if $is_note_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Notes'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Notes'} {if $count_moderate_total_note}<span class="badge">{count_moderate_total_note}</span>{/if}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('note','admin','unmoderated') }}" title="{lang 'Unmoderated Note Posts'}">{lang 'Unmoderated Notes'} {if $count_moderate_total_note}<span class="badge">{count_moderate_total_note}</span>{/if}</a></li>
                  <li><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Note Posts'}">{lang 'All Notes'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_webcam_enabled}
              <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picturewebcam') }}" title="{lang 'Moderate the Webcam Pictures'}"><i class="fa fa-camera"></i> {lang 'Webcam Pictures'}</a></li>
            {/if}
          </ul>
        </li>

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'file','index') }}" title="{lang 'File/Page CMS'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-file"></i> {lang 'File/Page'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display') }}" title="{lang 'Public File Manager'}"><i class="fa fa-file"></i> {lang 'Public Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display','protected') }}" title="{lang 'Protected File Manager'}"><i class="fa fa-file"></i> {lang 'Protected Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','pagedisplay') }}" title="{lang 'Display Static Pages'}"><i class="fa fa-pencil-square-o"></i> {lang 'Page Module'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','maildisplay') }}" title="{lang 'Display Email Template'}"><i class="fa fa-pencil"></i> {lang 'Email Template'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','themedisplay') }}" title="{lang 'Display all Templates Files'}"><i class="fa fa-paint-brush"></i> {lang 'Templates Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','suggestiondisplay') }}" title="{lang 'Suggestion List'}"><i class="fa fa-plus-circle"></i> {lang 'Suggestion List'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','bandisplay') }}" title="{lang 'Not Allowed Options'}"><i class="fa fa-ban"></i> {lang 'Not Allowed Options'}</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'Tools'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-wrench"></i> {lang 'Tool'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'General Tools'}"><i class="fa fa-database"></i> {lang 'Tools'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','envmode') }}" title="{lang 'Change the Environment Mode'}"><i class="fa fa-eye"></i> {lang 'Environment Mode'}</a></li>
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cache') }}" title="{lang 'Caches Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-tachometer"></i> {lang 'Caches'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cache') }}" title="{lang 'Caches Controls'}">{lang 'Caches Manager'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cacheconfig') }}" title="{lang 'Cache Settings'}">{lang 'Cache Setting'}</a></li>
              </ul>
            </li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','backup') }}" title="{lang 'Backup Manager'}"><i class="fa fa-floppy-o"></i> {lang 'Backup Manager'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','freeSpace') }}" title="{lang 'Free Space Server'}"><i class="fa fa-refresh"></i> {lang 'Free Space Server'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','blockcountry') }}" title="{lang 'Block Countries'}"><i class="fa fa-ban"></i> {lang 'Block Countries'}</a></li>
            <li class="menu-item dropdown dropdown-submenu">
              <a href="{{ $design->url(PH7_ADMIN_MOD,'info','index') }}" title="{lang 'Information'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
                <i class="fa fa-info-circle"></i> {lang 'Info'}
              </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','software') }}" title="{lang 'Information about the Software'}">{lang 'Software'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','language') }}" title="{lang 'PHP Info'}">{lang 'PHP Configuration'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'main','stat') }}" title="{lang 'Site Statistics'}">{lang 'Site Stats'}</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <li class="dropdown">
          <a class="bold dropdown-toggle" href="{software_doc_url}" title="{lang 'Need some Helps?'}" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-life-ring"></i> {lang 'Help'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a class="bold" href="{{ $design->url('ph7cms-helper','main','suggestionbox','?box=donationbox') }}" title="{lang 'Will You Be Nice Today? Like 81% of our users who contribute on a regular basis.'}"><i class="fa fa-trophy"></i> {lang 'Will You Be Nice Today?'} <span class="label label-primary">{lang 'HELP'}</span></a></li>
            <li><a href="{software_doc_url}" title="{lang 'Software Documentation'}"><i class="fa fa-book"></i> {lang 'Documentation'}</a></li>
            <li><a href="{software_issue_url}" title="{lang 'Report a Problem'}"><i class="fa fa-bug"></i> {lang 'Report a Bug'}</a></li>
            {* <li><a href="https://ph7cms-forum.com" title="{lang 'Discussions Board'}"><i class="fa fa-bug"></i> {lang 'Forums'}</a></li> *}
            <li><a href="{software_review_url}" title="{lang 'Help pH7CMS by giving a nice review! Highly appreciated :)'}"><i class="fa fa-heart"></i> {lang 'Give Nice Review'}</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="{{ $design->url(PH7_ADMIN_MOD,'account','index') }}" title="{lang 'My account'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">
            <i class="fa fa-cog"></i> {lang 'Account'} <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'account','edit') }}" title="{lang 'Edit My Account'}"><i class="fa fa-pencil fa-fw"></i> {lang 'Edit My Account'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'account','password') }}" title="{lang 'Change Password'}"><i class="fa fa-key fa-fw"></i> {lang 'Change Password'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'main','logout') }}" title="{lang 'Logout'}"><i class="fa fa-sign-out"></i> {lang 'Logout'}</a></li>
          </ul>
        </li>
      {/if}

      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

{* To switch back to admin panel from user session *}
  {if $is_admin_auth AND $admin_logged_as_user}
    <p class="center bold loginas">
      <a href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'logoutuseras') }}">{lang}Switch back to Admin Panel{/lang}</a>
    </p>
  {elseif $is_affiliate_enabled AND $is_admin_auth AND $admin_logged_as_affiliate}
    <p class="center bold loginas">
      <a href="{{ $design->url('affiliate', 'admin', 'logoutuseras') }}">{lang}Switch back to Admin Panel{/lang}</a>
    </p>
  {/if}
