  <nav class="bottom_nav">
      <div role="listbox" class="dropdown_menu ft_dm">
        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','about') }}" class="dropdown_item" data-load="ajax">{lang 'About %site_name%'}</a>
        </span>
        <ul class="show_dropdown">
          <li><a href="{{ $design->url('page','main','about') }}" title="{lang 'About Us'}" data-load="ajax">{lang 'About'}</a></li>
          <li><a href="{{ $design->url('page','main','helpus') }}" title="{lang 'Help Us'}" data-load="ajax">{lang 'Help'}</a></li>

          {if $is_blog_enabled}
            <li><a href="{{ $design->url('blog','main','index') }}" title="{lang 'Company Blog | News'}" data-load="ajax">{lang 'Blog'}</a></li>
          {/if}

          {if $is_affiliate_enabled}
            <li><a href="{{ $design->url('affiliate','home','index') }}" title="{lang 'Become an Affiliate'}">{lang 'Affiliate'}</a></li>
          {/if}

          <li><a href="{{ $design->url('contact','contact','index') }}" title="{lang 'Contact Us'}">{lang 'Contact'}</a></li>
          <li><a href="{{ $design->url('page','main','link') }}" title="{lang 'Links'}" data-load="ajax">{lang 'Links'}</a></li>
          <li><a href="{{ $design->url('page','main','job') }}" title="{lang 'Jobs | Careers'}" data-load="ajax">{lang 'Jobs'}</a></li>
          <li class="dm_self"><span class="dropdown_item">{lang 'About'}</span><i></i></li>
        </ul>
      </div> |

      <div role="listbox" class="dropdown_menu ft_dm">
        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','faq') }}" class="dropdown_item" data-load="ajax">{lang 'Help'}</a>
        </span>
        <ul class="show_dropdown">
          <li><a href="{{ $design->url('page','main','faq') }}" title="{lang 'Frequently Asked Questions'}">{lang 'FAQ'}</a></li>
          <li><a href="{{ $design->url('page','main','terms') }}" title="{lang 'Terms of Use'}" data-load="ajax">{lang 'Terms of Use'}</a></li>
          <li><a href="{{ $design->url('page','main','privacy') }}" title="{lang 'Privacy Policy'}" data-load="ajax">{lang 'Privacy'}</a></li>
          <li><a href="{{ $design->url('page','main','legalnotice') }}" title="{lang 'Legal Notice'}" data-load="ajax">{lang 'Legal Notice'}</a></li>
          <li class="dm_self"><span class="dropdown_item">{lang 'Help'}</span><i></i></li>
        </ul>
      </div> |

      {if !$is_user_auth AND $is_newsletter_enabled}
        <a href="{{ $design->url('newsletter','home','subscription') }}" title="{lang 'Subscribe to our newsletter!'}" data-popup="block-page">{lang 'Newsletter'}</a> |
      {/if}
      {if $is_invite_enabled}
        <a rel="nofollow" href="{{ $design->url('invite','home','invitation') }}" title="{lang 'Invite your friends!'}" data-popup="block-page">{lang 'Invite'}</a> |
      {/if}

      <a href="{{ $design->url('xml','sitemap','index') }}" title="{lang 'Site Map'}" data-load="ajax">{lang 'Site Map'}</a>
  </nav>
