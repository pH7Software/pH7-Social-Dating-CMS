    <nav class="bottom_nav" role="navigation">

      <div role="listbox" class="dropdown_menu ft_dm">
        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','about') }}" class="dropdown_item" data-load="ajax">{lang 'About %site_name%'}</a>
        </span>
        <ul class="show_dropdown">
          <li><a href="{{ $design->url('page','main','about') }}" title="{lang 'About Us'}" data-load="ajax">{lang 'About Us'}</a></li>
          <li><a href="{{ $design->url('page','main','helpus') }}" title="{lang 'Help Us'}" data-load="ajax">{lang 'Help Us'}</a></li>
          <li><a href="{{ $design->url('blog','main','index') }}" title="{lang 'Our Blog'}" data-load="ajax">{lang 'Blog'}</a></li>
          <li><a href="{{ $design->url('affiliate','home','index') }}" title="{lang 'Become an Affiliate'}">{lang 'Affiliate'}</a></li>
          <li><a href="{{ $design->url('contact','contact','index') }}" title="{lang 'Contact us'}">{lang 'Contact us'}</a></li>
          <li><a href="{{ $design->url('page','main','partner') }}" title="{lang 'Our Partners'}" data-load="ajax">{lang 'Partners'}</a></li>
          <li><a href="{{ $design->url('page','main','link') }}" title="{lang 'Links'}" data-load="ajax">{lang 'Links'}</a></li>
          <li><a href="{{ $design->url('page','main','job') }}" title="{lang 'Jobs | Careers'}" data-load="ajax">{lang 'Jobs'}</a></li>
          <li><a href="{{ $design->url('page','main','team') }}" title="{lang 'Our Team'}" data-load="ajax">{lang 'Team'}</a></li>
          <li class="dm_self"><span class="dropdown_item">{lang 'About'}</span><i></i></li>
        </ul>
      </div> |

      <div role="listbox" class="dropdown_menu ft_dm">
        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','help') }}" class="dropdown_item" data-load="ajax">{lang 'Help'}</a>
        </span>
        <ul class="show_dropdown">
          <li><a href="{{ $design->url('page','main','help') }}" title="{lang 'Help'}" data-load="ajax">{lang 'Help'}</a></li>
          <li><a href="{{ $design->url('page','main','faq') }}" title="{lang 'FAQ'}">{lang 'FAQ'}</a></li>
          <li><a href="{{ $design->url('page','main','terms') }}" title="{lang 'Terms of Use'}" data-load="ajax">{lang 'Terms of Use'}</a></li>
          <li><a href="{{ $design->url('page','main','privacy') }}" title="{lang 'Privacy'}" data-load="ajax">{lang 'Privacy'}</a></li>
          <li><a href="{{ $design->url('page','main','imprint') }}" title="{lang 'Imprint'}" data-load="ajax">{lang 'Imprint'}</a></li>
          <li class="dm_self"><span class="dropdown_item">{lang 'Help'}</span><i></i></li>
        </ul>
      </div> |

      {if !UserCore::auth()}<a href="{{ $design->url('newsletter','home','subscription') }}" title="{lang 'Subscribe to our newsletter!'}" data-popup="block-page">{lang 'Newsletter'}</a> |{/if}
      <a rel="nofollow" href="{{ $design->url('invite','home','invitation') }}" title="{lang 'Invite your friends!'}" data-popup="block-page">{lang 'Invite'}</a> |
      <a href="{{ $design->url('xml','sitemap','index') }}" title="{lang 'Site Map'}" data-load="ajax">{lang 'Site Map'}</a> |
      <a href="{{ $design->url('xml','rss','index') }}" title="{lang 'RSS Feed List'}" data-load="ajax">{lang 'RSS Feed'}</a>

    </nav>
