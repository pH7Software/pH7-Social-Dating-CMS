<div class="center">
    <div class="border m_marg vs_padd">
        <p class="bold">Clear Cache</p>
        <a href="javascript:void(0)" onclick="cache('general','{csrf_token}')">{lang 'Database and Other Data'}</a> &nbsp; &bull; &nbsp;
        <a href="javascript:void(0)" onclick="cache('tpl_compile','{csrf_token}')">{lang 'Server Code Template'}</a> &nbsp; &bull; &nbsp;
        <a href="javascript:void(0)" onclick="cache('tpl_html','{csrf_token}')">{lang 'HTML Template'}</a> &nbsp; &bull; &nbsp;
        <a href="javascript:void(0)" onclick="cache('static','{csrf_token}')">{lang 'Static Files'}</a>
    </div>

    <div class="s_marg">
        <script src="https://www.google.com/jsapi"></script>
        <script>
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(showCacheChart);

            function showCacheChart() {
                $('#cache_chart').html('');

                var oDataTable = new google.visualization.DataTable();
                oDataTable.addColumn('string', '{lang 'Cache'}');
                oDataTable.addColumn('number', '{lang 'Size'}');
                var aData = [
                    {each $aData in $aChartData}
                        ["{% $aData['title'] %}", {v:{% $aData['size'] %}, f:"{% Framework\File\Various::bytesToSize($aData['size']) %}"}],
                    {/each}
                ];
                oDataTable.addRows(aData);
                new google.visualization.PieChart($('#cache_chart')[0]).draw(oDataTable);
            }
        </script>
        <div id="cache_chart"></div>
    </div>
</div>
