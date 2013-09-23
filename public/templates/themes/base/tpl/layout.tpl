{* Last edit 09/24/13 01:16 by PH *}
{{ $design->htmlHeader() }}
<html lang="{% $config->values['language']['lang'] %}">
  <head>
    <meta charset="{% $config->values['language']['charset'] %}" />

    <!-- Begin Title and Meta -->
    <title>{if $page_title}{% $this->str->escape($this->str->upperFirst($page_title), true) %} - {site_name}{else}{site_name} - {slogan}{/if}</title>
    <meta name="description" content="{% $this->str->escape($this->str->upperFirst($meta_description), true) %}" />
    <meta name="keywords" content="{% $this->str->escape($meta_keywords, true) %}" />
    <link rel="shortcut icon" href="{url_relative}favicon.ico" />
    <link rel="canonical" href="{current_url}" />
    <link rel="author" href="{url_root}humans.txt" />
    <meta name="robots" content="{meta_robots}" />
    <meta name="author" content="{meta_author}"/>
    <meta name="copyright" content="{meta_copyright}" />
    <meta name="revisit-after" content="7 days" />
    <meta name="category" content="{meta_category}" />
    <meta name="rating" content="{meta_rating}"/>
    <meta name="distribution" content="{meta_distribution}"/>
    {if $header}{header}{/if}

    <!-- Begin Copyright pH7 Dating/Social CMS by Pierre-Henry SORIA, All Rights Reserved -->
    <!-- Do not modify or remove this code! Think of those who spend time to develop this framework and CMS for you. -->
    <meta name="creator" content="pH7 Team, Pierre-Henry Soria - {software_url}" />
    <meta name="designer" content="pH7 Team - {software_url}" />
    <meta name="generator" content="{software_name}  {software_version}" />
    <!-- End Copyright -->

    <!-- End Title and Meta -->

    <!-- Begin Sheet CSS -->
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" />
    {{ $design->staticFiles('css', PH7_STATIC . PH7_JS . 'jquery/box/', 'box.css') }}
    {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'common.css,style.css,layout.css,pagination.css,menu.css,like.css,color.css,alert-msg.css,form.css,js/jquery/rating.css,js/jquery/apprise.css,js/jquery/tipTip.css') }}

    {* Custom CSS code *}
    {{ $design->externalCssFile(PH7_RELATIVE.'asset/css/style.css') }}

    {if UserCore::auth()}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'messenger.css') }}
    {/if}

    <!-- Other sheet CSS for modules etc. -->
    {{ $design->css() }}
    {{ $designModel->files('css') }}
    <!-- End CSS -->

    <!-- Begin Header JavaScript -->
    <script>var pH7Url={base:'{url_root}',relative:'{url_relative}',tpl:'{url_tpl}',stic:'{url_static}',tplImg:'{url_tpl_img}',tplJs:'{url_tpl_js}',tplMod:'{url_tpl_mod}',data:'{url_data}'};</script>
    {if AdminCore::auth()}<script>pH7Url.admin_mod = '{url_admin_mod}';</script>{/if}

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <!--<script src="http://cdn.jquerytools.org/1.2.6/jquery.tools.min.js"></script>-->
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <!-- End Header JavaScript -->

    {* Begin AjPh *}
    {if $browser->isFullAjaxSite()}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'js/ajph.css') }}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'ajph.js') }}
    {/if}
    {* End AjPh *}

    {{ XmlDesignCore::sitemapHeaderLink() }}
    {{ XmlDesignCore::rssHeaderLinks() }}

    {{ $designModel->analyticsApi() }}
  </head>
  <body>

    <!-- Begin Header -->
    <header>

      <noscript>
        <div class="err_msg">{lang}Please enable JavaScript in your browser!<br />
        This site is not inconsistent activation of JavaScript, so it is necessary to activate it via the options of your browser.{/lang}</div>
      </noscript>

      <div role="banner" id="logo"><h1><a href="{url_root}" title="{slogan}">{site_name}</a></h1></div>
      <div role="banner" class="right ads_468_60">{{ $designModel->ads(468,60) }}</div>

    </header>
    <!-- End Header -->

    <!-- Begin Popups -->
    <div id="box">
      <p></p>
    </div>
    <!-- End Popups -->

    <!-- Begin Content -->
    <div role="main" id="content">

      {* If the splash page is not enabled, it displays the menu *}
      {if empty($is_splash_page)}
        {main_include 'top_menu.inc.tpl'}
      {/if}

      <div role="banner" class="right ads_120_600">{{ $designModel->ads(120,600) }}</div>
      <div role="banner" class="left ads_120_600">{{ $designModel->ads(120,600) }}</div>

      {* Headings group *}
      <div id="headings" class="center">
        {if !empty($h1_title )}
          <h1>{h1_title}</h1>
        {/if}
        {if !empty($h2_title )}
          <h2>{h2_title}</h2>
        {/if}
        {if !empty($h3_title )}
          <h3>{h3_title}</h3>
        {/if}
        {if !empty($h4_title )}
          <h4>{h4_title}</h4>
        {/if}
      </div>

      <br />

      {* Alert Message *}
      {{ $design->flashMsg() }}
      <div class="msg"></div>

      {* Loading JS Lang *}
      {* The file must be before the content of the site to avoid that the "pH7LangCore"  object is undefined *}
      {{ $lang_file =  Framework\Translate\Lang::getJsFile(PH7_PATH_STATIC . PH7_JS . PH7_LANG) }}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, PH7_LANG . $lang_file) }}

      <div id="ajph">
        <div id="sub_ajph">

          {if !empty($manual_include)}
            {manual_include $manual_include}
          {elseif !empty($pOH_not_found)}
            {main_include 'error.inc.tpl'}
          {else}
            {auto_include}
          {/if}

        </div>
      </div>

    </div>
    <!-- End Content -->

    <!-- Begin Footer -->
    <footer>
      <div role="banner" class="center ads_728_90">{{ $designModel->ads(728,90) }}</div>
      {{ $design->link() }}

      {* To avoid scammers *}
      {if UserCore::auth() && $current_url !== $url_root}
        <div class="warning_block center"><p>{lang}<strong>Attention!</strong> Some of the women (or men) profiles you see on dating sites might be scams to collect money.<br />
        People who is really interested in you will never ask for money.<br />
        Be careful, don\'t send the money to anybody!{/lang}</p></div>
      {/if}

      <div id="clock"></div>

      <div role="contentinfo">
        <div class="ft_copy"><p><strong>{site_name}</strong> &copy; <ph:date value="Y" /> - <strong><a href="http://cool-on-web.com" title="{lang 'Free Online Dating Site'}">{lang 'Online Dating Site'}</a></strong></p>{{ $design->littleLikeApi() }}</div> {{ $design->langList() }} {* {{ $designModel->langList() }} *} &nbsp;
        {main_include 'bottom_menu.inc.tpl'}
      </div>

      {if isDebug()}
        <div class="ft">
          <p>{{ $design->stat() }}</p>
          <p class="red">{lang 'WARNING: Your site is in development mode!'}</p>
        </div>
      {/if}
    </footer>
    <!-- End Footer -->

    <!-- Begin Footer JavaScript -->
    {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/box/box.js,jquery/tipTip.js,common.js,clock.js,str.js') }}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS, 'global.js') }}

    {* SetUserActivity & User Chat *}
    {if UserCore::auth()}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'setUserActivity.js,jquery/sound.js') }}
      {{ $lang_file = Framework\Translate\Lang::getJsFile(PH7_PATH_TPL_SYS_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_JS . PH7_LANG) }}
      {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_JS, PH7_LANG . $lang_file . ',jquery.cookie.js,Messenger.js') }}
    {/if}

    {* JS code Injection *}
    {{ $design->externalJsFile(PH7_RELATIVE.'asset/js/script.js') }}

    <!-- Other JavaScript files for modules etc. -->
    {{ $design->js() }}
    {{ $designModel->files('js') }}

    {if UserCore::auth()}
      {main_include 'favicon_alert.inc.tpl'}
    {/if}

    <!-- Common Dialog -->
    {{ $design->message() }}
    {{ $design->error() }}
    <!-- End Footer JavaScript -->

{{ $design->htmlFooter() }}
