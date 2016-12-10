    {* Get the frequently used functions in variables to optimize the script and call those functions only once in the file *}
    {{
      $admin_logged_as_user = UserCore::isAdminLoggedAs();
      $admin_logged_as_affiliate = AffiliateCore::isAdminLoggedAs()
     }}


    {* Creating Objects *}
      {{ $oSession = new Framework\Session\Session() }}

    {* Menu for All *}
      <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div role="banner" id="logo" class="navbar-brand"><h1><a href="{{ $design->homePageUrl() }}" title="{slogan}">{site_name}</a></h1></div>
          </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

    {* Guest Menu *}

      {if !$is_user_auth AND !$is_aff_auth AND !$is_admin_auth}

        <li><a class="bold" href="{{ $design->url('user','signup','step1') }}" title="{lang 'Join Now!'}"><i class="fa fa-user-plus"></i> {lang 'Join Now!'}</a></li>
        <li><a href="{{ $design->url('user', 'main','login') }}" title="{lang 'Login'}" data-load="ajax"><i class="fa fa-sign-in"></i> {lang 'Login'}</a></li>

      {/if}


    {* Menu Guest, Member and Admin *}

      {if !$is_aff_auth}

        <li class="dropdown"><a href="{{ $design->url('user', 'browse', 'index') }}" title="{lang 'Members'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-user fa-fw"></i> {lang 'People'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url('user', 'browse', 'index') }}" rel="nofollow" title="{lang 'Members'}" data-load="ajax"><i class="fa fa-users"></i> {lang 'People'}</a></li>

            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('user','search', 'index') }}" title="{lang 'Search the members'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-search"></i> {lang 'Search'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('user', 'search', 'quick') }}" title="{lang 'Quick Search'}" data-load="ajax">{lang 'Quick Search'}</a></li>
                <li><a href="{{ $design->url('user', 'search', 'advanced') }}" title="{lang 'Advanced Search'}" data-load="ajax">{lang 'Advanced Search'}</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url('user','country','index',$country.PH7_SH.$city) }}" title="{lang 'Users in %0% through the Map!',$city}"><i class="fa fa-map-marker"></i> {lang 'Users in your Area'}</a></li>

            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('user', 'birthday', 'index') }}" title="{lang 'Users Birthday'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-birthday-cake"></i> {lang 'Birthday'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('user', 'birthday', 'index', 'all') }}" rel="nofollow" title="{lang 'All Birthday'}" data-load="ajax">{lang 'All Birthday'}</a></li>
                <li><a href="{{ $design->url('user', 'birthday', 'index', 'male') }}" title="{lang 'Men Birthday'}" data-load="ajax">{lang 'Men Birthday'}</a></li>
                <li><a href="{{ $design->url('user', 'birthday', 'index', 'female') }}" title="{lang 'Women Birthday'}" data-load="ajax">{lang 'Women Birthday'}</a></li>
                <li><a href="{{ $design->url('user', 'birthday', 'index', 'couple') }}" title="{lang 'Couples Birthday'}" data-load="ajax">{lang 'Couples Birthday'}</a></li>
              </ul>
            </li>
          </ul>
        </li>
      {/if}


    {* Menu Guest, Member and LoginUserAs of Admin Panel *}

      {if ( !$is_aff_auth AND !$is_admin_auth ) OR $admin_logged_as_user }

        {if $is_chat_enabled OR $is_chatroulette_enabled}
          <li class="dropdown"><a href="{{ $design->url('chat','home','index') }}" title="{lang 'The Free Chat Rooms'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-weixin"></i> {lang 'Chat'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              {if $is_chat_enabled}
                <li><a href="{{ $design->url('chat','home','index') }}" rel="nofollow" title="{lang 'Video Chat'}" data-load="ajax"><i class="fa fa-weixin"></i> {lang 'Chat'}</a></li>
              {/if}

              {if $is_chatroulette_enabled}
                <li><a href="{{ $design->url('chatroulette','home','index') }}" title="{lang 'Chat Roulette'}"><i class="fa fa-random"></i> {lang 'Chatroulette'}</a></li>
              {/if}

            </ul>
          </li>
        {/if}

        {if $is_picture_enabled}
          <li class="dropdown"><a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Photo'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('picture','main','index') }}" rel="nofollow" title="{lang 'Photo Gallery'}" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Photos'}</a></li>

              {if $is_hotornot_enabled}
                <li><a href="{{ $design->url('hotornot','main','rating') }}" title="{lang 'Hot Or Not'}" data-load="ajax"><i class="fa fa-star-half-o"></i> {lang 'Hot Or Not'}</a></li>
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
            <li class="dropdown"><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'My Emails'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-envelope-o fa-fw"></i> {lang 'Mail'} <span class="badge">{count_unread_mail}</span> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('mail','main','compose') }}" title="{lang 'Compose'}"><i class="fa fa-pencil"></i> {lang 'Compose'}</a></li>
                <li><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'Inbox - Messages Received'}"><i class="fa fa-inbox"></i> {lang 'Inbox'}</a></li>
                <li><a href="{{ $design->url('mail','main','outbox') }}" title="{lang 'Messages Sent'}"><i class="fa fa-paper-plane-o"></i> {lang 'Sent'}</a></li>
                <li><a href="{{ $design->url('mail','main','trash') }}" title="{lang 'Trash'}"><i class="fa fa-trash-o"></i> {lang 'Trash'}</a></li>
                <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Find Messages'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
              </ul>
            </li>
          {/if}

          <noscript>
            <li class="dropdown"><a href="{{ $design->url('user','setting','edit') }}" title="{lang 'Settings'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">{lang 'Settings'} <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url('user','setting','edit') }}">{lang 'Edit Profile'}</a></li>
                <li><a href="{{ $design->url('user','setting','design') }}">{lang 'Design Profile'}</a></li>
                <li><a href="{{ $design->url('user','setting','notification') }}">{lang 'Notifications'}</a></li>
                <li><a href="{{ $design->url('user','setting','privacy') }}">{lang 'Privacy Settings'}</a></li>
                {if $is_valid_license}<li><a href="{{ $design->url('payment','main','info') }}">{lang 'Membership Details'}</a></li>{/if}
                <li><a href="{{ $design->url('user','setting','password') }}">{lang 'Change Password'}</a></li>
              </ul>
            </li>
          </noscript>

          <li class="dropdown"><a href="{{ $design->url('user','account','index') }}" title="{lang 'My Account'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-cog"></i> {lang 'Account'} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ $design->url('user','setting','index') }}" title="{lang 'My Settings'}"><i class="fa fa-cog fa-fw"></i> {lang 'Edit Profile'}</a></li>
              <li><a href="{% (new UserCore)->getProfileLink($oSession->get('member_username')) %}" title="{lang 'See My Profile'}"><i class="fa fa-user fa-fw"></i> {lang 'See My Profile'}</a></li>
              <li><a href="{{ $design->url('user','setting','avatar') }}" title="{lang 'Change Profile Photo'}"><i class="fa fa-upload"></i> {lang 'Change Profile Photo'}</a></li>

              {if $is_picture_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Photo Gallery'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('picture','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                    <li><a href="{{ $design->url('picture','main','albums', $oSession->get('member_username')) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                  </ul>
                </li>
              {/if}

              {if $is_video_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('video','main','index') }}" title="{lang 'Videos Gallery'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown" data-load="ajax"><i class="fa fa-youtube-play"></i> {lang 'Videos Gallery'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('video','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                    <li><a href="{{ $design->url('video','main','albums', $oSession->get('member_username')) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                  </ul>
                </li>
              {/if}

              {if $is_note_enabled}
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('note','main','index') }}" title="{lang 'Notes'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Note'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('note','main','add') }}" title="{lang 'Add a Note'}"><i class="fa fa-newspaper-o"></i> {lang 'Add a Note'}</a></li>
                    <li><a href="{{ $design->url('note','main','author', $oSession->get('member_username')) }}" title="{lang 'My Notes'}">{lang 'My Notes'}</a></li>
                  </ul>
                </li>
              {/if}

              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('user','friend','index') }}" title="{lang 'Friends Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-users"></i> {lang 'Friends Manager'} <span class="badge">{count_pen_friend_request}</span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('user','friend','index') }}" title="{lang 'Friends List'}">{lang 'Friends List'}</a></li>
                  <li><a href="{{ $design->url('user','friend','search',$oSession->get('member_username')) }}" title="{lang 'Find a friend in my list'}">{lang 'Find a Friend'}</a></li>
                </ul>
              </li>

              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-eye"></i> {lang 'Who See Me'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}">{lang 'Who See Me'}</a></li>
                  <li><a href="{{ $design->url('user','visitor','search') }}" title="{lang 'Find who visited my profile'}">{lang 'Find Visitor(s)'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('user','main','logout') }}" title="{lang 'Logout'}"><i class="fa fa-sign-out"></i> {lang 'Logout'}</a></li>
            </ul>
          </li>

      {/if}


    {* Affiliate Menu *}

      {if $is_aff_auth AND ( !$is_user_auth AND !$is_admin_auth OR $admin_logged_as_affiliate ) }

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

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'user','index') }}" title="{lang 'Users/Admins Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> {lang 'User/Admin'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Browse Users'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user"></i> {lang 'Users'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Browse Users'}"><i class="fa fa-users"></i> {lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','search') }}" title="{lang 'Search Users'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
                <li><a href="{{ $design->url('report','admin','index') }}" title="{lang 'Reports'}"><i class="fa fa-question"></i> {lang 'Reports'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','add') }}" title="{lang 'Add a User'}"><i class="fa fa-user-plus"></i> {lang 'Add User'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','addfakeprofiles') }}" title="{lang 'Fake Profiles Automatic Generator'}"><i class="fa fa-user-plus"></i> {lang 'Fake Profile Generator'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','import') }}" title="{lang 'Import Users'}"><i class="fa fa-user-plus"></i> {lang 'Import Users'}</a></li>
                <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'User Fields'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-check-circle-o"></i> {lang 'User Fields'}</a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'Fields List'}">{lang 'Fields List'}</a></li>
                    <li><a href="{{ $design->url('field','field','add','user') }}" title="{lang 'Add Fields'}">{lang 'Add Fields'}</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Browse Admins'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user"></i> {lang 'Admins'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Browse Admins'}"><i class="fa fa-users"></i> {lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','search') }}" title="{lang 'Search an Admin'}"><i class="fa fa-search"></i> {lang 'Search'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','add') }}" title="{lang 'Add an Admin'}"><i class="fa fa-user-plus"></i> {lang 'Add Admin'}</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'Settings'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-cog fa-fw"></i> {lang 'Settings'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'General Settings'}"><i class="fa fa-tachometer"></i> {lang 'General'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting','metamain') }}" title="{lang 'Meta Tags/Homepage Texts'}"><i class="fa fa-tag"></i> {lang 'Meta Tags/Homepage Texts'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','ads') }}" title="{lang 'Add Banners on the best click-through-rate locations'}"><i class="fa fa-money"></i> {lang 'Ad Banners'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','analyticsapi') }}" title="{lang 'Analytics Code'}"><i class="fa fa-bar-chart"></i> {lang 'Analytics Code'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'style') }}" title="{lang 'Custom CSS Style'}"><i class="fa fa-code"></i> {lang 'CSS Style'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'script') }}" title="{lang 'JavaScript Injection'}"><i class="fa fa-code"></i> {lang 'JavaScript'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'license') }}" title="{lang 'License Key'}"><i class="fa fa-key"></i> {lang 'License'}</a></li>
          </ul>
        </li>

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'module','index') }}" title="{lang 'Modules Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-puzzle-piece"></i> {lang 'Mods'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','disable') }}" title="{lang 'Enable/Disable System Modules'}"><i class="fa fa-toggle-on"></i> {lang 'Enable/Disable Modules'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','index') }}" title="{lang 'Third-party Modules Manager'}"><i class="fa fa-plug"></i> {lang '3rd-party Mods Manager'}</a></li>

            {if $is_newsletter_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Newsletters'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}">{lang 'Newsletters'}</a></li>
                  <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown">{lang 'Subscribers'}</a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}">{lang 'Browse'}</a></li>
                      <li><a href="{{ $design->url('newsletter', 'admin', 'search') }}" title="{lang 'Search Subscribers'}">{lang 'Search'}</a></li>
                    </ul>
                  </li>
                </ul>
              </li>
            {/if}

            {if $is_forum_enabled}
              <li><a href="{{ $design->url('forum','admin','index') }}" title="{lang 'Admin Forum'}"><i class="fa fa-comments"></i> {lang 'Forum'}</a></li>
            {/if}

            {if $is_blog_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('blog','admin','index') }}" title="{lang 'Admin Blog'}"><i class="fa fa-commenting-o"></i> {lang 'Blog'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('blog','admin','index') }}" title="{lang 'Admin Control - Blog'}">{lang 'Admin Blog'}</a></li>
                  <li><a href="{{ $design->url('blog','admin','add') }}" title="{lang 'Add a Blog Post'}">{lang 'Add a Post'}</a></li>
                </ul>
              </li>
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
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('affiliate','admin','index') }}" title="{lang 'Affiliate Admin Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-money"></i> {lang 'Affiliate'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('affiliate','admin','banner') }}" title="{lang 'Banners Manager'}">{lang 'Banners'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','browse') }}" title="{lang 'Affiliates List'}">{lang 'Browse Affiliates'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','search') }}" title="{lang 'Search an Affiliate'}">{lang 'Search an Affiliate'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','add') }}" title="{lang 'Add an Affiliate'}">{lang 'Add Affiliate'}</a></li>
                  <li><a href="{{ $design->url('affiliate','admin','config') }}" title="{lang 'Affiliate Settings'}">{lang 'Settings'}</a></li>
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
                <li><a href="{{ $design->url('payment','admin','membershiplist') }}" title="{lang 'Memberships List'}">{lang 'Memberships List'}</a></li>
                <li><a href="{{ $design->url('payment','admin','addmembership') }}" title="{lang 'Membership Manager'}">{lang 'Add a new Membership'}</a></li>
                <li><a href="{{ $design->url('payment','admin','config') }}" title="{lang 'Payment Gateway Config'}">{lang 'Gateway Config'}</a></li>
              </ul>
            </li>

            {if $is_mail_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('mail','admin','index') }}" title="{lang 'Member Email Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-envelope-o"></i> {lang 'Email Manager'}</a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('mail','admin','msglist') }}" title="{lang 'Messages Monitor'}">{lang 'Messages Monitor'}</a></li>
                  <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Search Email'}">{lang 'Search Message'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_video_enabled}
              <li><a href="{{ $design->url('video', 'admin', 'config') }}"><i class="fa fa-youtube-play"></i> {lang 'Video Youtube API key'}</a></li>
            {/if}

            {if $is_connect_enabled}
              <li><a href="{{ $design->url('connect', 'admin', 'config') }}"><i class="fa fa-share-alt-square"></i> {lang 'Universal Login Config'}</a></li>
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

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','index') }}" title="{lang 'User Moderation'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-user-secret"></i> {lang 'Moderation'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picturealbum') }}" title="{lang 'Moderate Photo Albums'}"><i class="fa fa-picture-o"></i> {lang 'Photo Album'} <span class="badge">{count_moderate_total_picture_album}</span></a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picture') }}" title="{lang 'Moderate Pictures'}"><i class="fa fa-picture-o"></i> {lang 'Picture'} <span class="badge">{count_moderate_total_picture}</span></a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','videoalbum') }}" title="{lang 'Moderate Video Albums'}"><i class="fa fa-youtube-play"></i> {lang 'Video Album'} <span class="badge">{count_moderate_total_video_album}</span></a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','video') }}" title="{lang 'Moderate Videos'}"><i class="fa fa-youtube-play"></i> {lang 'Video'} <span class="badge">{count_moderate_total_video}</span></a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','avatar') }}" title="{lang 'Moderate Avatars'}"><i class="fa fa-picture-o"></i> {lang 'Avatar'} <span class="badge">{count_moderate_total_avatar}</span></a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','background') }}" title="{lang 'Moderate Profile Background'}"><i class="fa fa-picture-o"></i> {lang 'Profile Background'} <span class="badge">{count_moderate_total_background}</span></a></li>

            {if $is_note_enabled}
              <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Notes'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-newspaper-o"></i> {lang 'Notes'} <span class="badge">{count_moderate_total_note}</span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ $design->url('note','admin','unmoderated') }}" title="{lang 'Unmoderated Note Posts'}">{lang 'Unmoderated Notes'} <span class="badge">{count_moderate_total_note}</span></a></li>
                  <li><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Note Posts'}">{lang 'All Notes'}</a></li>
                </ul>
              </li>
            {/if}

            {if $is_webcam_enabled}
              <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picturewebcam') }}" title="{lang 'Moderate the Webcam Pictures'}"><i class="fa fa-camera"></i> {lang 'Webcam Pictures'}</a></li>
            {/if}

          </ul>
        </li>

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'file','index') }}" title="{lang 'File/Page CMS'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-file"></i> {lang 'File/Page'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display') }}" title="{lang 'Public File Manager'}"><i class="fa fa-file"></i> {lang 'Public Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display','protected') }}" title="{lang 'Protected File Manager'}"><i class="fa fa-file"></i> {lang 'Protected Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','pagedisplay') }}" title="{lang 'Display Page of Module'}"><i class="fa fa-pencil-square-o"></i> {lang 'Page Module'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','maildisplay') }}" title="{lang 'Display Email Template'}"><i class="fa fa-pencil"></i> {lang 'Email Template'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','themedisplay') }}" title="{lang 'Display all Templates Files'}"><i class="fa fa-paint-brush"></i> {lang 'Templates Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','suggestiondisplay') }}" title="{lang 'Suggestion List'}"><i class="fa fa-plus-circle"></i> {lang 'Suggestion List'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','bandisplay') }}" title="{lang 'Ban Options'}"><i class="fa fa-ban"></i> {lang 'Ban Options'}</a></li>
          </ul>
        </li>

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'Tool'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-wrench"></i> {lang 'Tools'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'General Tools'}"><i class="fa fa-database"></i> {lang 'Tools'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','envmode') }}" title="{lang 'Change the Environment Mode'}"><i class="fa fa-eye"></i> {lang 'Environment Mode'}</a></li>
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cache') }}" title="{lang 'Caches Manager'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-tachometer"></i> {lang 'Caches Manager'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cache') }}" title="{lang 'Caches Controls'}">{lang 'Caches Manager'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cacheconfig') }}" title="{lang 'Cache Settings'}">{lang 'Cache Setting'}</a></li>
              </ul>
            </li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','backup') }}" title="{lang 'Backup Manager'}"><i class="fa fa-floppy-o"></i> {lang 'Backup Manager'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','freeSpace') }}" title="{lang 'Free Space Server'}"><i class="fa fa-refresh"></i> {lang 'Free Space Server'}</a></li>
            <li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url(PH7_ADMIN_MOD,'info','index') }}" title="{lang 'Information'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-info-circle"></i> {lang 'Info'}</a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'main','stat') }}" title="{lang 'Site Statistics'}">{lang 'Site Stats'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','software') }}" title="{lang 'Software'}">{lang 'Software'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','language') }}" title="{lang 'PHP Info'}">{lang 'PHP'}</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <li class="dropdown"><a class="bold" href="{software_help_url}" title="{lang "Need some Helps? We're here for you!"}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-life-ring"></i> {lang 'Help'} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{software_help_url}" title="{lang 'Need Professional Technical Supports?'}"><i class="fa fa-life-ring"></i> {lang 'Technical Support'}</a></li>
            <li><a href="{software_doc_url}" title="{lang 'Software Documentation'}"><i class="fa fa-book"></i> {lang 'Documentation'}</a></li>
            <li><a href="http://ph7cms.com/how-to-report-bugs" title="{lang 'Report a Problem'}"><i class="fa fa-bug"></i> {lang 'Report a Bug'}</a></li>
            {* Coming soon ...
            <li><a href="{software_faq_url}" title="{lang 'Frequently Asked Questions'}">{lang 'Faq'}</a></li>
            <li><a href="{software_forum_url}" title="{lang 'Support Forum'}">{lang 'Forum'}</a></li>
            *}
            {if !$is_valid_license}
                <li><a class="bold underline" href="{software_license_url}" title="{lang 'Buy a License Key'}"><i class="fa fa-key"></i> {lang 'Switch to pH7CMSPro'}</a></li>
            {/if}
          </ul>
        </li>

        <li class="dropdown"><a href="{{ $design->url(PH7_ADMIN_MOD,'account','index') }}" title="{lang 'My account'}" class="dropdown-toggle" role="button" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-cog"></i> {lang 'Account'} <span class="caret"></span></a>
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

{* For LoginUserAs of Admin Panel *}
  {if $is_admin_auth AND $admin_logged_as_user }
    <p class="center bold loginas"><a href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'logoutuseras') }}">{lang}Click here to switch back to the Admin Panel{/lang}</a></p>
  {elseif $is_admin_auth AND $admin_logged_as_affiliate }
    <p class="center bold loginas"><a href="{{ $design->url('affiliate', 'admin', 'logoutuseras') }}">{lang}Click here to switch back to the Admin Panel{/lang}</a></p>
  {/if}

    {* Destroy the varaibles *}
      {{
        unset(
          $oSession,
          $count_moderate_total_picture_album,
          $count_moderate_total_picture,
          $count_moderate_total_video_album,
          $count_moderate_total_video,
          $count_moderate_total_avatar,
          $count_moderate_total_background,
          $count_moderate_total_note
        )
      }}
