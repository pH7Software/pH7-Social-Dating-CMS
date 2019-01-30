<!DOCTYPE html>
<html lang="{$LANG.lang}">
    <head>
        <meta charset="{$LANG.charset}" />
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

        <!-- Begin Title and Meta Info -->
        <title>{$LANG.welcome|upper} «{$software_name|upper}» {$software_version}</title>
        <link rel="icon" href="{$smarty.const.PH7_URL_ROOT}favicon.ico" />
        <meta name="robots" content="noindex, nofollow" />
        <!---- Copyright pH7 Dating/Social CMS; All Rights Reserved ---->
        <meta name="author" content="{$software_author}" />
        <meta name="copyright" content="{$software_copyright}" />
        <meta name="creator" content="pH7CMS (Pierre-Henry Soria)" />
        <meta name="designer" content="pH7CMS (Pierre-Henry Soria)" />
        <meta name="generator" content="{$software_name} {$software_version}" />
        <!---- End Copyright pH7 Dating/Social CMS; All Rights Reserved ---->
        <!-- End Title and Meta -->
        <!-------- Sheet Css -------->
        <!-- Bootstrap -->
        <link rel="stylesheet" href="{$smarty.const.PH7_URL_ROOT}static/css/bootstrap.css" />
        <!-- pH7CMS CSS -->
        <link rel="stylesheet" media="all" href="{$smarty.const.PH7_URL_INSTALL}themes/{$tpl_name}/css/common.css" />
        <!-------- End Css -------->
        <script>var sInstallUrl = "{$smarty.const.PH7_URL_INSTALL}";</script>
    </head>
    <body>
        <div role="main" class="center">
            <!-- Begin Header -->
            <header>
                <div role="banner" id="logo">
                    <h1><a href="{$smarty.const.PH7_URL_ROOT}"><img src="{$smarty.const.PH7_URL_ROOT}templates/themes/base/img/logo.png" alt="{$software_name|upper}" title="{$software_name|upper}" /> pH7CMS</a></h1>
                </div>

                <noscript>
                    <div class="err_msg">{$LANG.warning_no_js}</div>
                </noscript>

                {if !empty($sept_number)}
                    <h1>{$LANG.welcome_to_installer} {$software_name} - {$LANG.step} {$sept_number}/{$total_install_steps}</h1>
                {/if}
            </header>
            <!-- End Header -->
            <div id="particles-js"></div>

            {if !empty($sept_number)}
                {assign var="progressbar_percentage" value=$sept_number*14.3}

                <div class="progress">
                    <div
                        class="progress-bar progress-bar-striped active"
                        role="progressbar"
                        aria-valuenow="{$progressbar_percentage}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        style="width:{$progressbar_percentage}%"
                    >{$progressbar_percentage}%
                    </div>
                </div>
            {/if}
