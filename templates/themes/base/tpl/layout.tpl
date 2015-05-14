{* Store "UserCore::auth()" function in a variable in order to optimize the script and call this function only once in the file *}
{{ $is_user_auth = UserCore::auth() }}

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
    {if !$is_user_auth}{{ $design->regionalUrls() }}{/if}
    <link rel="author" href="{url_root}humans.txt" />
    <meta name="robots" content="{meta_robots}" />
    <meta name="author" content="{meta_author}"/>
    <meta name="copyright" content="{meta_copyright}" />
    <meta name="revisit-after" content="7 days" />
    <meta name="category" content="{meta_category}" />
    <meta name="rating" content="{meta_rating}"/>
    <meta name="distribution" content="{meta_distribution}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    {if $header}{header}{/if}

    <!-- Begin Copyright pH7 Dating/Social CMS by Pierre-Henry SORIA, All Rights Reserved -->
    <!-- Do not modify or remove this code! Think of those who spend a lot of time to develop this CMS & Framework for you -->
    <meta name="creator" content="pH7CMS, Pierre-Henry Soria - {software_url}" />
    <meta name="designer" content="pH7CMS, Pierre-Henry Soria - {software_url}" />
    <meta name="generator" content="{software_name}  {software_version}" />
    <!-- End Copyright -->

    <!-- End Title and Meta -->

    <!-- Begin Sheet CSS -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    {{ $design->staticFiles('css', PH7_STATIC . PH7_JS . 'jquery/box/', 'box.css') }} {* We have to include box CSS alone because it includes images in its folder *}
    {{ $design->staticFiles('css', PH7_STATIC . PH7_CSS, 'bootstrap.css,bootstrap_customize.css') }}
    {{ $design->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'common.css,style.css,layout.css,menu.css,like.css,color.css,form.css,js/jquery/rating.css,js/jquery/apprise.css,js/jquery/tipTip.css') }}

    {* Custom CSS code *}
    {{ $design->externalCssFile(PH7_RELATIVE.'asset/css/style.css') }}

    {if $is_user_auth}
      {{ $design->staticFiles('css', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'messenger.css') }}
    {/if}

    <!-- Other sheet CSS for modules etc. -->
    {{ $design->css() }}
    {{ $designModel->files('css') }}
    <!-- End CSS -->

    <!-- Begin Header JavaScript -->
    <script>var pH7Url={base:'{url_root}',relative:'{url_relative}',tpl:'{url_tpl}',stic:'{url_static}',tplImg:'{url_tpl_img}',tplJs:'{url_tpl_js}',tplMod:'{url_tpl_mod}',data:'{url_data}'};</script>
    {if AdminCore::auth()}<script>pH7Url.admin_mod = '{url_admin_mod}';</script>{/if}
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
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
        <div class="err_msg">{lang}JavaScript is disabled on your Web browser!<br /> Please enable JavaScript via the options of your Web browser in order to use this website.{/lang}</div>
      </noscript>

      <div role="banner" id="logo"><h1><a href="{url_root}" title="{slogan}">{site_name}</a></h1></div>
      <div role="banner" class="right ad_468_60">{{ $designModel->ad(468,60) }}</div>

    </header>
    <!-- End Header -->

    <!-- Begin Popups -->
    <div id="box">
      <p></p>
    </div>
    <!-- End Popups -->

    <!-- Begin Content -->
    <div role="main" class="container" id="content">

      {* If the splash page is not enabled, it displays the menu *}
      {if !(!$is_user_auth && $this->registry->module == 'user' && $this->registry->controller == 'MainController' && $this->registry->action == 'index')}
        {main_include 'top_menu.inc.tpl'}
      {/if}

      <div role="banner" class="right ad_160_600">{{ $designModel->ad(160,600) }}</div>
      <div role="banner" class="left ad_160_600">{{ $designModel->ad(160,600) }}</div>

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
      <div role="banner" class="center ad_728_90">{{ $designModel->ad(728,90) }}</div>
      {{ $design->link() }}

      {* To avoid scammers *}
      {if $is_user_auth && $current_url != $url_root}
        <div class="warning_block center"><p>{lang}<strong>Attention!</strong> Some of the women (or men) profiles you see on dating sites might be scams to collect money.<br />
        People who is really interested in you will never ask for money.<br />
        Be careful, don\'t send the money to anybody!{/lang}</p></div>
      {/if}

      <div role="contentinfo">
        <div class="ft_copy"><p><strong>{site_name}</strong> &copy; <ph:date value="Y" /></p> {{ $design->littleLikeApi() }}</div>{{ $design->langList() }}
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
    {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/box/box.js,jquery/tipTip.js,bootstrap.js,common.js,str.js,holder.js') }}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS, 'global.js') }}

    {* SetUserActivity & User Chat *}
    {if $is_user_auth}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'setUserActivity.js,jquery/sound.js') }}
      {{ $lang_file = Framework\Translate\Lang::getJsFile(PH7_PATH_TPL_SYS_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_JS . PH7_LANG) }}
      {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'im/' . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_JS, PH7_LANG . $lang_file . ',jquery.cookie.js,Messenger.js') }}
    {/if}

    {* Cookie info bar *}
    {if $is_cookie_consent_bar}
      {{ $design->staticFiles('js', PH7_STATIC . PH7_JS . 'cookie_consent/', 'library.js,bar.js') }}
    {/if}

    {* JS code Injection *}
    {{ $design->externalJsFile(PH7_RELATIVE.'asset/js/script.js') }}

    <!-- Other JavaScript files for modules etc. -->
    {{ $design->js() }}
    {{ $designModel->files('js') }}

    {if $is_user_auth}
      {main_include 'favicon_alert.inc.tpl'}
    {/if}

    <!-- Common Dialog -->
    {{ $design->message() }}
    {{ $design->error() }}
    {if $is_disclaimer}
      {main_include 'disclaimer.inc.tpl'}
    {/if}

    <!-- End Footer JavaScript -->

{* Destroy the variable *}
{{ unset($is_user_auth) }}

{{ $design->htmlFooter() }}
