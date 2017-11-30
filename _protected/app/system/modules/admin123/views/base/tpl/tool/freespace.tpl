<div class="center">
    <div class="s_marg">
        <script src="https://www.google.com/jsapi"></script>
        <script>
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(showFreeSpaceChart);

            function showFreeSpaceChart () {
                $('#free_space_chart').html('');

                var oDataTable = new google.visualization.DataTable();
                oDataTable.addColumn('string', '{lang 'Free Space'}');
                oDataTable.addColumn('number', '{lang 'Size'}');
                var aData = [
                    {each $aData in $aChartData}
                        ["{% $aData['title'] %}", {v:{% $aData['size'] %}, f:"{% Framework\File\Various::bytesToSize($aData['size']) %}"}],
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
