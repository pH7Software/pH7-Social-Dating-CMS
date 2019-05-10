<div id="elfinder"></div>
{* Include elFinder in a simple "script src" instead of Design::addCss() because it has some issues when elfinder.js is compressed and/or has gzip compression  *}
<script src="{url_static}fileManager/js/elfinder.js"></script>

<script>
    $(function () {
        $('#elfinder').elfinder({
            url: pH7Url.base + '{url_admin_mod}asset/ajax/fileManager/{type}Connector/'
        }).elfinder('instance');
    });
</script>
