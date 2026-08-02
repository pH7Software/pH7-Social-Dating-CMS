<div class="center">
    <div class="s_marg">
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
            google.charts.load('current', {packages: ['corechart']});
            google.charts.setOnLoadCallback(showFreeSpaceChart);

            function showFreeSpaceChart () {
                $('#free_space_chart').html('');

                var oDataTable = new google.visualization.DataTable();
                oDataTable.addColumn('string', {% json_encode(t('Free Space')) %});
                oDataTable.addColumn('number', {% json_encode(t('Size')) %});
                var aData = [
                    {each $aData in $aChartData}
                        [{% json_encode($aData['title']) %}, {v: {% $aData['size'] %}, f: {% json_encode(Framework\File\Various::bytesToSize($aData['size'])) %}}],
                    {/each}
                ];
                oDataTable.addRows(aData);
                new google.visualization.PieChart($('#free_space_chart')[0]).draw(oDataTable);
            }
        </script>
        <div id="free_space_chart"></div>
    </div>

    <p class="red">
        {lang 'Note: If all folders are on the same hard disk, you will have the same size everywhere.'}
    </p>
</div>
