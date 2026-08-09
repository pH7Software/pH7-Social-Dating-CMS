{{ $design->htmlHeader() }}
{{ $design->softwareComment() }}
<html lang="{% $config->values['language']['lang'] %}">
  <head>
    <meta charset="{% $config->values['language']['charset'] %}" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <!-- Begin Title and Meta info -->
    <title>{if $page_title}{% $str->escape($str->upperFirst($page_title), true) %} - {site_name}{else}{site_name} - {slogan}{/if}</title>
    <meta name="description" content="{% $str->escapeAttribute($str->upperFirst($meta_description)) %}" />
    <meta name="keywords" content="{% $str->escapeAttribute($meta_keywords) %}" />
    {main_include 'social-meta-tags.inc.tpl'}

    {{
      $aJsonLdWebsite = [
        '@context' => 'https://schema.org',
        '@type' => ['WebSite', 'SocialNetworkingSite', 'DatingService'],
        'name' => $site_name,
        'url' => PH7_URL_ROOT,
        'logo' => PH7_URL_ROOT . 'favicon.ico',
        'publishingPrinciples' => \PH7\Framework\Mvc\Router\Uri::get('page', 'main', 'aipolicy')
      ]
    }}
    <script type="application/ld+json">{% json_encode($aJsonLdWebsite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) %}</script>
    <meta name="robots" content="{% $str->escapeAttribute($meta_robots) %}" />
    <link rel="icon" href="{url_relative}favicon.ico" />
    <link rel="canonical" href="{current_url}" />
    <link rel="author" href="{url_root}humans.txt" />
    {if !$is_user_auth}{{ $design->regionalUrls() }}{/if}
    <meta name="author" content="{% $str->escapeAttribute($meta_author) %}" />
    <meta name="copyright" content="{% $str->escapeAttribute($meta_copyright) %}" />
    <meta name="category" content="{% $str->escapeAttribute($meta_category) %}" />
    <meta name="rating" content="{% $str->escapeAttribute($meta_rating) %}" />
    <meta name="distribution" content="{% $str->escapeAttribute($meta_distribution) %}" />
    {if $header}{header}{/if}

    {if $is_pwa_enabled}
      <link rel="manifest" href="{{ $design->url('pwa','main','manifest') }}" />
      <meta name="msapplication-config" content="{{ $design->url('pwa','main','browserconfig') }}" />
      <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)" />
      <meta name="theme-color" content="#15151c" media="(prefers-color-scheme: dark)" />
      {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'pwa/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_JS, 'sw-register.js') }}
      {main_include 'pwa-icon-tags.inc.tpl'}
    {/if}

    <!-- Software generator metadata -->
    <meta name="generator" content="{software_name}, v{software_version}" />

    <!-- End Title and Meta -->

    <!-- Begin Sheet CSS -->
    {{ $design->externalCssFile(PH7_URL_STATIC. PH7_CSS . 'js/jquery/smoothness/jquery-ui.css') }}
    {{ $design->externalCssFile(PH7_URL_STATIC. PH7_CSS . 'font-awesome.css') }}
    {{ $design->staticFiles('css', PH7_STATIC . PH7_CSS . 'js/jquery/box', 'box.css') }} {* We have to include box CSS alone because it includes images in its folder *}
    {{ $design->staticFiles('css', PH7_STATIC . PH7_CSS, 'bootstrap.css,bootstrap_customize.css,animate.css') }}
    {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'common.css,style.css,layout.css,like.css,color.css,form.css,js/jquery/rating.css,js/jquery/apprise.css,js/jquery/tipTip.css,design_system.css') }}
    {if $top_navbar_type === 'inverse'}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'menu_inverse.css') }}
    {else}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'menu.css') }}
    {/if}
    {* System font stack is defined in design_system.css — no external font request (better privacy/performance) *}

    {* Custom CSS code *}
    {{ $design->externalCssFile(PH7_RELATIVE.'asset/css/color.css') }}
    {{ $design->externalCssFile(PH7_RELATIVE.'asset/css/style.css') }}

    {if $is_user_auth AND $is_im_enabled}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'messenger.css') }}
    {/if}

    {if $is_disclaimer}
      {{ $design->staticFiles('css', PH7_STATIC . PH7_CSS . PH7_JS, 'disclaimer.css') }}
    {/if}

    <!-- Other sheet CSS for modules etc. -->
    {{ $design->css() }}
    {designModel.files('css')}
    <!-- End CSS -->

    <!-- Begin Header JavaScript -->
    <script>var pH7Url={base:'{url_root}',relative:'{url_relative}',tpl:'{url_tpl}',stic:'{url_static}',tplImg:'{url_tpl_img}',tplJs:'{url_tpl_js}',tplMod:'{url_tpl_mod}',data:'{url_data}',csrf:{% json_encode((new Framework\Security\CSRF\Token)->url(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) %}};</script>
    {if $is_admin_auth}<script>pH7Url.admin_mod = '{url_admin_mod}';</script>{/if}
    {{ $design->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery.js') }}
    <!-- End Header JavaScript -->

    {{ XmlDesignCore::sitemapHeaderLink() }}
    {{ XmlDesignCore::rssHeaderLinks() }}

    {designModel.analyticsApi()}
  </head>
  <body itemscope="itemscope" itemtype="http://schema.org/WebPage">

    <!-- Begin Header -->
    <header>
      {* If we aren't on the the splash page, then display the menu *}
      {if !$is_guest_homepage}
        {main_include 'top_menu.inc.tpl'}
      {/if}

      <noscript>
        <div class="noscript err_msg">
          {lang}JavaScript is disabled on your Web browser!<br /> Please enable JavaScript via the options of your Web browser in order to use this website.{/lang}
        </div>
      </noscript>

      {if $is_guest_homepage}
          <div class="row">
              <div role="banner" id="logo" class="col-md-8">
                  <h1 itemprop="name">
                      <a href="{{ $design->homePageUrl() }}">{site_name}</a>
                  </h1>
              </div>
          </div>
      {/if}

      {* Heading groups (H1 to H4) *}
      {main_include 'headings.inc.tpl'}

      {* Don't display the top middle banner on the the splash page *}
      {if !$is_guest_homepage}
          <div role="banner" class="center ad_468_60">
              {designModel.ad(468, 60)}
          </div>
      {/if}

      <div class="clear"></div>
    </header>
    <!-- End Header -->

    <!-- Begin Popups -->
    <div id="box">
      <p></p>
    </div>
    <!-- End Popups -->

    <!-- Begin Content -->
    <main role="main" class="container" id="content">
      {* Alert Message *}
      {{ $design->flashMsg() }}
      <div class="msg"></div>

      {* Loading JS Lang *}
      {* The file must be before the content of the site to avoid that the "pH7LangCore"  object is undefined *}
      {{ $lang_file =  Framework\Translate\Lang::getJsFile(PH7_PATH_STATIC . PH7_JS . PH7_LANG) }}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, PH7_LANG . $lang_file) }}

      {if !empty($manual_include)}
        {manual_include $manual_include}
      {elseif !empty($pOH_not_found)}
        {main_include 'error.inc.tpl'}
      {else}
        {auto_include}
      {/if}
    </main>
    <div role="banner" class="center ad_468_60">
        {designModel.ad(468, 60)}
    </div>
    <!-- End Content -->

    <!-- Begin Footer -->
    <footer>
      <div role="banner" class="center ad_728_90">
          {designModel.ad(728, 90)}
      </div>

      <div role="contentinfo">
        <div class="ft_copy">
          {{ $design->littleSocialMediaWidgets() }}

          <p>
            &copy; <ph:date value="Y" /> <strong>{site_name}</strong>  {{ $design->link() }}
          </p>
        </div>
        {{ $design->langList() }}
        {main_include 'bottom_menu.inc.tpl'}
      </div>

      {if isDebug()}
        <div class="ft">
          <p><small>{{ $design->stat() }}</small></p>
        </div>
        <p class="small dark-red">
          {lang 'WARNING: Your site is in development mode! You can change the mode'} <a href="{{ $design->url(PH7_ADMIN_MOD,'tool','envmode') }}" title="{lang 'Change the Environment Mode'}" class="dark-red">{lang 'here'}</a>
        </p>
      {/if}
    </footer>

    <div class="clear"></div>
    <div class="right vs_marg">
      {* Required for the GeoLite2 free version. Not needed if you purchase their full paid version *}
      <small class="small">
        {lang}We use GeoLite2 from <a href="https://www.maxmind.com" rel="nofollow" class="gray">MaxMind</a>{/lang}
      </small>
    </div>
    <!-- End Footer -->

    <!-- Begin Footer JavaScript -->
    {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/box.js,jquery/tipTip.js,bootstrap.js,common.js,str.js,holder.js,password_toggle.js') }}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS, 'global.js') }}
    {{ $design->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery-ui.js') }} {* UI must be the last here, otherwise the jQueryUI buttons won't work *}

    {* SetUserActivity and User Chat *}
    {if $is_user_auth}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'setUserActivity.js,jquery/sound.js') }}

      {if $is_im_enabled}
        {{ $lang_file = Framework\Translate\Lang::getJsFile(PH7_PATH_TPL_SYS_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_JS . PH7_LANG) }}
        {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_JS, PH7_LANG . $lang_file . ',jquery.cookie.js,Messenger.js') }}
      {/if}
    {/if}

    {* Cookie info bar *}
    {if $is_cookie_consent_bar}
      <script
        src="https://cdn.jsdelivr.net/npm/cookie-bar@1.10.3/cookiebar-latest.min.js?always=1"
        integrity="sha384-uLZyDSeIDuxDTgQovVDh6PEtbh5UJDfdGWeEo5F3w9gEeIUHdqqowv84xY3IYhJO"
        crossorigin="anonymous"
      ></script>
    {/if}

    {* JS code Injection *}
    {{ $design->externalJsFile(PH7_RELATIVE.'asset/js/script.js') }}

    <!-- Other JavaScript files for modules etc. -->
    {{ $design->js() }}
    {designModel.files('js')}

    {if $is_user_auth}
      {main_include 'favicon_alert.inc.tpl'}
    {/if}

    <!-- Common Dialogs -->
    {{ $design->message() }}
    {{ $design->error() }}

    {if $is_disclaimer AND !AdminCore::isAdminPanel()}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS . 'disclaimer', 'Disclaimer.js,common.js') }}
      {main_include 'disclaimer-dialog.inc.tpl'}
    {/if}
    <!-- End Footer JavaScript -->

{{ $design->htmlFooter() }}
