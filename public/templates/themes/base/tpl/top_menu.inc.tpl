
    {* Creating Objects *}
      {{ $oSession = new Framework\Session\Session() }}


    {* For LoginUserAs of Admin Panel *}
      {if AdminCore::auth() && $oSession->exists('login_user_as') }
        <p class="bold center"><a href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'logoutuseras') }}">{lang}Click here to switch back to admin panel.{/lang}</a></p>
      {elseif  AdminCore::auth() && $oSession->exists('login_affiliate_as') }
        <p class="bold center"><a href="{{ $design->url('affiliate', 'admin', 'logoutuseras') }}">{lang}Click here to switch back to admin panel.{/lang}</a></p>
      {/if}


    {* Menu for All *}
      <nav role="navigation">
        <ul class="top_nav">
          <li{if $current_url == $url_root} class="current"{/if}><a href="{if AdminCore::auth()}{{ $design->url(PH7_ADMIN_MOD,'main','index') }}{else}{url_root}{/if}" title="{lang 'Home'}">{lang 'Home'}</a></li>


    {* Guest Menu *}

      {if !UserCore::auth() && !AffiliateCore::auth() && !AdminCore::auth() }

        <li><a href="{{ $design->url('user','signup','step1') }}" title="{lang 'Join Now!'}">{lang 'Join Now!'}</a></li>
        <li><a href="{{ $design->url('user', 'main','login') }}" title="{lang 'Login'}" data-load="ajax">{lang 'Login'}</a></li>

      {/if}


    {* Menu Guest, Member and Admin *}

      {if !AffiliateCore::auth()}

        <li><a href="{{ $design->url('user', 'browse', 'index') }}" title="{lang 'Members'}" data-load="ajax">{lang 'People'}</a>
          <ul>
            <li><a href="{{ $design->url('user', 'browse', 'index') }}" rel="nofollow" title="{lang 'Members'}" data-load="ajax">{lang 'People'}</a></li>

            <li><a href="{{ $design->url('user','search', 'index') }}" title="{lang 'Search the members'}" data-load="ajax">{lang 'Search'}</a>
              <ul>
                <li><a href="{{ $design->url('user', 'search', 'quick') }}" title="{lang 'Quick Search'}" data-load="ajax">{lang 'Quick Search'}</a></li>
                <li><a href="{{ $design->url('user', 'search', 'advanced') }}" title="{lang 'Advanced Search'}" data-load="ajax">{lang 'Advanced Search'}</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url('user', 'birthday', 'index') }}" title="{lang 'Users Birthday'}" data-load="ajax">{lang 'Birthday'}</a>
              <ul>
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

      {if ( !AffiliateCore::auth() && !AdminCore::auth() ) || $oSession->exists('login_user_as') }

        <li><a href="{{ $design->url('chat','home','index') }}" title="{lang 'The Free Chat Rooms'}" data-load="ajax">{lang 'Chat Rooms'}</a>
          <ul>
            <li><a href="{{ $design->url('chat','home','index') }}" rel="nofollow" title="{lang 'Video Chat'}" data-load="ajax">{lang 'Chat'}</a></li>
            <li><a href="{{ $design->url('chatroulette','home','index') }}" title="{lang 'Chat Roulette'}">{lang 'Chatroulette'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" data-load="ajax">{lang 'Pictures'}</a>
          <ul>
            <li><a href="{{ $design->url('picture','main','index') }}" rel="nofollow" title="{lang 'Photo Gallery'}" data-load="ajax">{lang 'Pictures'}</a></li>
            <li><a href="{{ $design->url('hotornot','main','rating') }}" title="{lang 'Hot Or Not'}" data-load="ajax">{lang 'Hot Or Not'}</a></li>
            <li><a href="{{ $design->url('picture','main','search') }}" title="{lang 'Search a Picture'}" data-load="ajax">{lang 'Search'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url('video','main','index') }}" title="{lang 'Video Gallery'}" data-load="ajax">{lang 'Videos'}</a>
          <ul>
            <li><a href="{{ $design->url('video','main','index') }}" rel="nofollow" title="{lang 'Video Gallery'}" data-load="ajax">{lang 'Videos'}</a></li>
            <li><a href="{{ $design->url('video','main','search') }}" title="{lang 'Search a Video'}" data-load="ajax">{lang 'Search'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url('game','main','index') }}" title="{lang 'Games Zone'}" data-load="ajax">{lang 'Game'}</a>
          <ul>
            <li><a href="{{ $design->url('game','main','index') }}" rel="nofollow" title="{lang 'Games Zone'}" data-load="ajax">{lang 'Game'}</a></li>
            <li><a href="{{ $design->url('game','main','search') }}" title="{lang 'Search a Game'}" data-load="ajax">{lang 'Search'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url('forum','forum','index') }}" title="{lang 'Forums'}" data-load="ajax">{lang 'Forum'}</a>
          <ul>
           <li><a href="{{ $design->url('forum','forum','index') }}" rel="nofollow" title="{lang 'Forums'}" data-load="ajax">{lang 'Forum'}</a></li>
            <li><a href="{{ $design->url('forum','forum','search') }}" title="{lang 'Search a Topic'}" data-load="ajax">{lang 'Search'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url('note','main','index') }}" title="{lang 'Community Notes'}" data-load="ajax">{lang 'Notes'}</a>
          <ul>
            <li><a href="{{ $design->url('note','main','index') }}" rel="nofollow" title="{lang 'Community Notes'}" data-load="ajax">{lang 'Notes'}</a></li>
            <li><a href="{{ $design->url('note','main','search') }}" title="{lang 'Search a Note'}" data-load="ajax">{lang 'Search'}</a></li>
          </ul>
        </li>

      {/if}


    {* Member Menu *}

        {if UserCore::auth() && ( !AffiliateCore::auth() && !AdminCore::auth() ) || $oSession->exists('login_user_as') }

          <li><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'My Emails'}">{lang 'Email'} ({count_unread_mail})</a>
            <ul>
              <li><a href="{{ $design->url('mail','main','compose') }}" title="{lang 'Compose'}">{lang 'Compose'}</a></li>
              <li><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'Inbox'}">{lang 'Inbox'}</a></li>
              <li><a href="{{ $design->url('mail','main','outbox') }}" title="{lang 'Outbox'}">{lang 'Outbox'}</a></li>
             <li><a href="{{ $design->url('mail','main','trash') }}" title="{lang 'Trash'}">{lang 'Trash'}</a></li>
             <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Search'}">{lang 'Search'}</a></li>
           </ul>
         </li>

          <li><a href="{{ $design->url('user','setting','index') }}" title="{lang 'My settings'}">{lang 'Settings'}</a>
            <noscript>
            <ul>
              <li><a href="{{ $design->url('user','setting','edit') }}">{lang 'Edit Profile'}</a></li>
              <li><a href="{{ $design->url('user','setting','design') }}">{lang 'Design Profile'}</a></li>
              <li><a href="{{ $design->url('user','setting','notification') }}">{lang 'Notifications'}</a></li>
              <li><a href="{{ $design->url('user','setting','privacy') }}">{lang 'Privacy Setting'}</a></li>
              <li><a href="{{ $design->url('user','setting','password') }}">{lang 'Change Password'}</a></li>
            </ul>
            </noscript>
          </li>

          <li><a href="{{ $design->url('user','account','index') }}" title="{lang 'My account'}">{lang 'Account'}</a>
            <ul>
              <li><a href="{% (new UserCore)->getProfileLink($oSession->get('member_username')) %}" title="{lang 'My Profilet'}">{lang 'My Profile'}</a></li>
              <li><a href="{{ $design->url('user','setting','avatar') }}" title="{lang 'My Avatar'}">{lang 'My Avatar'}</a></li>
              <li><a href="{{ $design->url('picture','main','index') }}" title="{lang 'Photo Gallery'}" data-load="ajax">{lang 'Photo Gallery'}</a>
                <ul>
                  <li><a href="{{ $design->url('picture','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                  <li><a href="{{ $design->url('picture','main','albums', $oSession->get('member_username')) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('video','main','index') }}" title="{lang 'Videos Gallery'}" data-load="ajax">{lang 'Videos'}</a>
                <ul>
                  <li><a href="{{ $design->url('video','main','addalbum') }}" title="{lang 'Add an Album'}">{lang 'Add an Album'}</a></li>
                  <li><a href="{{ $design->url('video','main','albums', $oSession->get('member_username')) }}" title="{lang 'My Albums'}" data-load="ajax">{lang 'My Albums'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('note','main','index') }}" title="{lang 'Notes'}">{lang 'Notes'}</a>
                <ul>
                  <li><a href="{{ $design->url('note','main','add') }}" title="{lang 'Add a Note'}">{lang 'Add a Note'}</a></li>
                  <li><a href="{{ $design->url('note','main','author', $oSession->get('member_username')) }}" title="{lang 'My Notes'}">{lang 'My Notes'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('user','friend','index') }}" title="{lang 'Friends Management'}">{lang 'Friends Management'} ({count_pen_friend_request})</a>
                <ul>
                  <li><a href="{{ $design->url('user','friend','index') }}" title="{lang 'Friends List'}">{lang 'Friends List'}</a></li>
                  <li><a href="{{ $design->url('user','friend','search',$oSession->get('member_username')) }}" title="{lang 'Find a friend in my list'}">{lang 'Find a Friend'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}">{lang 'Who See Me'}</a>
                <ul>
                  <li><a href="{{ $design->url('user','visitor','index') }}" title="{lang 'Who Visited My Profile'}">{lang 'Who See Me'}</a></li>
                  <li><a href="{{ $design->url('user','visitor','search') }}" title="{lang 'Find someone who has visited my profile'}">{lang 'Find some Visitor(s)'}</a></li>
                </ul>
              </li>

              <li><a href="{{ $design->url('user','main','logout') }}" title="{lang 'Logout'}">{lang 'Logout'}</a></li>
            </ul>
          </li>

      {/if}


    {* Affiliate Menu *}

      {if AffiliateCore::auth() && ( !UserCore::auth() && !AdminCore::auth() || $oSession->exists('login_affiliate_as') ) }

        <li><a href="{{ $design->url('affiliate','ads','index') }}" title="{lang 'Gets Banners'}">{lang 'Banners'}</a></li>

        <li><a href="{{ $design->url('affiliate','account','index') }}" title="{lang 'My account'}">{lang 'Account'}</a>
          <ul>
            <li><a href="{{ $design->url('affiliate','account','index') }}" title="{lang 'My account'}">{lang 'Account'}</a></li>
            <li><a href="{{ $design->url('affiliate','account','edit') }}" title="{lang 'Edit account'}">{lang 'Edit Account'}</a></li>
            <li><a href="{{ $design->url('affiliate','account','password') }}" title="{lang 'Change Password'}">{lang 'Change Password'}</a></li>
            <li><a href="{{ $design->url('affiliate','home','logout') }}" title="{lang 'Logout'}">{lang 'Logout'}</a></li>
          </ul>
        </li>

      {/if}


    {* Admin Menu *}

      {if AdminCore::auth() && ( !UserCore::auth() && !AffiliateCore::auth() ) }

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','index') }}" title="{lang 'Users/Admins'}">{lang 'Users'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Browse Users'}">{lang 'Users'}</a>
              <ul>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}" title="{lang 'Browse Users'}">{lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','search') }}" title="{lang 'Search a Users'}">{lang 'Search'}</a></li>
                <li><a href="{{ $design->url('report','admin','index') }}" title="{lang 'Report'}">{lang 'Report'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','add') }}" title="{lang 'Add User'}">{lang 'Add User'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'user','import') }}" title="{lang 'Import Users'}">{lang 'Import Users'}</a></li>
                <li><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'User Fields'}">{lang 'User Fields'}</a>
                  <ul>
                    <li><a href="{{ $design->url('field','field','all','user') }}" title="{lang 'Fields List'}">{lang 'Fields List'}</a></li>
                    <li><a href="{{ $design->url('field','field','add','user') }}" title="{lang 'Add a Field'}">{lang 'Add a Field'}</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Browse Admins'}">{lang 'Admins'}</a>
              <ul>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}" title="{lang 'Browse Admins'}">{lang 'Browse'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','search') }}" title="{lang 'Search an Admin'}">{lang 'Search'}</a></li>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'admin','add') }}" title="{lang 'Add Admin'}">{lang 'Add Admin'}</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'Settings'}">{lang 'Settings'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','index') }}" title="{lang 'General Settings'}">{lang 'General'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting','metamain') }}" title="{lang 'Settings'}">{lang 'Meta Tags'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','ads') }}" title="{lang 'Advertisement'}">{lang 'Advertisement'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting','analyticsapi') }}" title="{lang 'Analytics Code'}">{lang 'Analytics Code'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'style') }}" title="{lang 'Custom Style'}">{lang 'Style'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'script') }}" title="{lang 'JavaScript Injection'}">{lang 'Script'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'setting', 'license') }}" title="{lang 'License Key'}">{lang 'License'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','index') }}" title="{lang 'Information'}">{lang 'Info'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'main','stat') }}" title="{lang 'Site Statistics'}">{lang 'Site Stats'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','software') }}" title="{lang 'Software'}">{lang 'Software'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'info','language') }}" title="{lang 'PHP Info'}">{lang 'PHP'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','index') }}" title="{lang 'Modules Manager'}">{lang 'Modules'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'module','index') }}" title="{lang 'Modules Manager'}">{lang 'Modules Manager'}</a></li>
            <li><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}">{lang 'Newsletters'}</a>
              <ul>
                <li><a href="{{ $design->url('newsletter', 'admin', 'index') }}" title="{lang 'Mass Mailer'}">{lang 'Newsletters'}</a></li>
                <li><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}">{lang 'Subscribers'}</a>
                  <ul>
                    <li><a href="{{ $design->url('newsletter', 'admin', 'browse') }}" title="{lang 'Browse Subscribers'}">{lang 'Browse'}</a></li>
                    <li><a href="{{ $design->url('newsletter', 'admin', 'search') }}" title="{lang 'Search Subscribers'}">{lang 'Search'}</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <li><a href="{{ $design->url('forum','admin','index') }}" title="{lang 'Forum Admin'}">{lang 'Forum'}</a></li>
            <li><a href="{{ $design->url('blog','admin','index') }}" title="{lang 'Blog Admin'}">{lang 'Blog'}</a></li>
            <li><a href="{{ $design->url('game','admin','index') }}" title="{lang 'Game Admin'}">{lang 'Game'}</a>
              <ul>
                <li><a href="{{ $design->url('game','admin','add') }}" title="{lang 'Add a Game'}">{lang 'Add a Game'}</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url('affiliate','admin','index') }}" title="{lang 'Affiliate Admin Manager'}">{lang 'Affiliate'}</a>
              <ul>
                <li><a href="{{ $design->url('affiliate','admin','banner') }}" title="{lang 'Banners Manager'}">{lang 'Banners'}</a></li>
                <li><a href="{{ $design->url('affiliate','admin','browse') }}" title="{lang 'Affiliates List'}">{lang 'Browse Affiliates'}</a></li>
                <li><a href="{{ $design->url('affiliate','admin','search') }}" title="{lang 'Search an Affiliate'}">{lang 'Search an Affiliate'}</a></li>
                <li><a href="{{ $design->url('affiliate','admin','add') }}" title="{lang 'Add Affiliate'}">{lang 'Add Affiliate'}</a></li>
                <li><a href="{{ $design->url('field','field','all','aff') }}" title="{lang 'Affiliate Fields'}">{lang 'Affiliate Fields'}</a>
                  <ul>
                    <li><a href="{{ $design->url('field','field','all','aff') }}" title="{lang 'Fields List'}">{lang 'Fields List'}</a></li>
                    <li><a href="{{ $design->url('field','field','add','aff') }}" title="{lang 'Add a Field'}">{lang 'Add a Field'}</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <li><a href="{{ $design->url('payment','admin','index') }}" title="{lang 'Payment System'}">{lang 'Payment'}</a>
              <ul>
                <li><a href="{{ $design->url('payment','admin','membershiplist') }}" title="{lang 'Memberships List'}">{lang 'Memberships List'}</a></li>
                <li><a href="{{ $design->url('payment','admin','addmembership') }}" title="{lang 'Membership Management'}">{lang 'Add a new Membership'}</a></li>
                <li><a href="{{ $design->url('payment','admin','config') }}" title="{lang 'Payment Gateway Config'}">{lang 'Gateway Config'}</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url('mail','admin','index') }}" title="{lang 'Email Management'}">{lang 'Email Management'}</a>
              <ul>
                <li><a href="{{ $design->url('mail','admin','msglist') }}" title="{lang 'Messages Monitor'}">{lang 'Messages Monitor'}</a></li>
                <li><a href="{{ $design->url('mail','main','search') }}" title="{lang 'Search Email'}">{lang 'Search Message'}</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url('connect', 'admin', 'config') }}">{lang 'Universal Login Config'}</a></li>

          </ul>
        </li>

        {* Moderate Count *}
          {{
            $oModeratorModel = new ModeratorCoreModel();

            $count_moderate_total_album_picture = $oModeratorModel->totalAlbumsPicture();
            $count_moderate_total_picture = $oModeratorModel->totalPictures();
            $count_moderate_total_album_video = $oModeratorModel->totalAlbumsVideo();
            $count_moderate_total_video = $oModeratorModel->totalVideos();
            $count_moderate_total_avatar = $oModeratorModel->totalAvatars();
            $count_moderate_total_background = $oModeratorModel->totalBackgrounds();
            $count_moderate_total_note = $oModeratorModel->totalNotes();

            unset($oModeratorModel);
          }}

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','index') }}" title="{lang 'User Moderation'}">{lang 'Moderation'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','albumpicture') }}" title="{lang 'Moderate Albums'}">{lang 'Picture Album'} ({count_moderate_total_album_picture})</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picture') }}" title="{lang 'Moderate Pictures'}">{lang 'Picture'} ({count_moderate_total_picture})</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','albumvideo') }}" title="{lang 'Moderate Albums'}">{lang 'Video Album'} ({count_moderate_total_album_video})</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','video') }}" title="{lang 'Moderate Videos'}">{lang 'Video'} ({count_moderate_total_video})</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','avatar') }}" title="{lang 'Moderate Avatars'}">{lang 'Avatar'} ({count_moderate_total_avatar})</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','background') }}" title="{lang 'Moderate Profile Background'}">{lang 'Profile Background'} ({count_moderate_total_background})</a></li>

            <li><a href="{{ $design->url('note','admin','index') }}" title="{lang 'Moderate Note'}">{lang 'Note'} ({count_moderate_total_note})</a>
              <ul>
                <li><a href="{{ $design->url('note','admin','unmoderated') }}" title="{lang 'Moderate the Note Posts'}">{lang 'Note Posts'} ({count_moderate_total_note})</a></li>
              </ul>
            </li>

            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'moderator','picturewebcam') }}" title="{lang 'Moderate the Pictures Webcam'}">{lang 'Pictures Webcam'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','index') }}" title="{lang 'File Management'}">{lang 'Files'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display') }}" title="{lang 'Public File Management'}">{lang 'Public Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','display','protected') }}" title="{lang 'Protected File Management'}">{lang 'Protected Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','pagedisplay') }}" title="{lang 'Display Page of Module'}">{lang 'Page Module'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','maildisplay') }}" title="{lang 'Display Email Template'}">{lang 'Email Template'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','themedisplay') }}" title="{lang 'Display the all Templates Files'}">{lang 'Templates Files'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','suggestiondisplay') }}" title="{lang 'Suggestion List'}">{lang 'Suggestion List'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'file','bandisplay') }}" title="{lang 'Ban Options'}">{lang 'Ban Options'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'Tools'}">{lang 'Tools'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','index') }}" title="{lang 'Tools'}">{lang 'Tools'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','backup') }}" title="{lang 'Backup Management'}">{lang 'Backup Management'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cache') }}" title="{lang 'Caches Controls'}">{lang 'Caches Management'}</a>
              <ul>
                <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','cacheconfig') }}" title="{lang 'Cache Settings'}">{lang 'Cache Settings'}</a></li>
              </ul>
            </li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'tool','freeSpace') }}" title="{lang 'Free Space Server'}">{lang 'Free Space Server'}</a></li>
          </ul>
        </li>

        <li><a href="{{ $design->url(PH7_ADMIN_MOD,'account','index') }}" title="{lang 'Your Account'}">{lang 'Account'}</a>
          <ul>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'account','edit') }}" title="{lang 'Edit your Account'}">{lang 'Edit Account'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'account','password') }}" title="{lang 'Change Password'}">{lang 'Change Password'}</a></li>
            <li><a href="{{ $design->url(PH7_ADMIN_MOD,'main','logout') }}" title="{lang 'Logout'}">{lang 'Logout'}</a></li>
          </ul>
        </li>

        <li><a href="{software_help_url}" title="{lang "Need some Helps? We're here for you!"}">{lang 'Help'}</a>
          <ul>
            <li><a href="{software_help_url}" title="{lang 'Individual Ticket Support'}">{lang 'Support'}</a></li>
            <li><a href="{software_doc_url}" title="{lang 'Software Documentation'}">{lang 'Documentation'}</a></li>
          {* Coming soon *}
          {*
            <li><a href="{software_faq_url}" title="{lang 'Frequently Asked Questions'}">{lang 'Faq'}</a></li>
            <li><a href="{software_forum_url}" title="{lang 'Support Forum'}">{lang 'Forum'}</a></li>
          *}
            <li><a href="{software_license_url}" title="{lang 'Buy a License Key'}">{lang 'License'}</a></li>
          </ul>
        </li>

      {/if}

      </ul>
    </nav>

    {{ unset($oSession) }}
