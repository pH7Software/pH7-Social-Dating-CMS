<!DOCTYPE html>
<html lang="{$LANG.lang}">
    <head>
        <meta charset="{$LANG.charset}" />
        <!-- Begin Title and Meta -->
        <title>{$LANG.welcome|upper} «{$software_name|upper}» {$software_version}</title>
        <link rel="shortcut icon" href="{$smarty.const.PH7_URL_ROOT}favicon.ico" />
        <!-- Copyright pH7 Dating/Social CMS; All Rights Reserved -->
        <meta name="author" content="{$software_author}" />
        <meta name="creator" content="pH7 CMS (Pierre-Henry Soria)" />
        <meta name="designer" content="pH7 CMS (Pierre-Henry Soria)" />
        <meta name="generator" content="{$software_name} {$software_version}" />
        <!-- End Copyright pH7 Dating/Social CMS; All Rights Reserved -->
        <!-- End Title and Meta -->
        <!-- Sheet Css -->
        <link rel="stylesheet" media="all" href="{$smarty.const.PH7_URL_INSTALL}themes/{$tpl_name}/css/common.css" />
        <!-- End Css -->
        <script>var sInstallUrl = "{$smarty.const.PH7_URL_INSTALL}";</script>
    </head>
    <body>
        <div class="center">
            <!-- Begin Header -->
            <header>
                <p><a href="{$smarty.const.PH7_URL_ROOT}"><img src="{$smarty.const.PH7_URL_ROOT}templates/themes/base/img/logo.png" alt="{$software_name|upper}" title="{$software_name|upper}" /></a></p>

                {if !empty($sept_number)}
                    <h1>{$LANG.welcome_to_installer} {$software_name} - {$LANG.step} {$sept_number}/6</h1>
                {/if}
            </header>
            <!-- End Header -->
