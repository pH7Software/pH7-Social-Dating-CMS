<script>
$(function() { // $(document).ready()
   var elf = $('#elfinder').elfinder({
     url: pH7Url.base + '{url_admin_mod}asset/ajax/fileManager/{type}Connector/'
   }).elfinder('instance');
});
</script>

<div id="elfinder"></div>
