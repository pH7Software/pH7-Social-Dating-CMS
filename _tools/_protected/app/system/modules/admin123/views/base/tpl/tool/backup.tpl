<form method="post" action="{current_url}" enctype="multipart/form-data">
    {{ $designSecurity->inputToken('backup') }}

    <table class="center">
        {if !empty($msg_success)}
            <tr>
                <td class="green1 bold">{msg_success}</td>
            </tr>
        {/if}

        <tr>
            <td class="bold">{lang 'Database Backup'}</td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="server">{lang 'Save SQL file to your server'}
                    <input type="radio" name="backup_type" value="server" id="server" />
                </label>
            </td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="server_archive">
                    {lang 'Save Gzip Archive (.gz) to your server'}
                    <input type="radio" name="backup_type" value="server_archive" id="server_archive" />
                </label>
            </td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="client">
                    {lang 'Download SQL file to your desktop'}
                    <input type="radio" name="backup_type" value="client" id="client" />
                </label>
            </td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="client_archive">
                    {lang 'Download Gzip Archive (.gz) to your desktop'}
                    <input type="radio" name="backup_type" value="client_archive" id="client_archive" />
                </label>
            </td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="show">
                    {lang 'Show on the screen'}
                    <input type="radio" name="backup_type" value="show" id="show" />
                </label>
                <input type="submit" name="backup" value="{lang 'Backup'}" />
            </td>
        </tr>

        {if !empty($sql_content)}
            <tr>
              <td class="bold">{lang 'View Database'}</td>
            </tr>

            <tr>
                <td class="border vs_padd">
                    <textarea id="backup">{sql_content}</textarea>
                </td>
            </tr>
        {/if}


        <tr>
            <td class="bold">{lang 'Database Restore from server'}</td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="dump_file">{lang 'Please select a dump file'}
                    <select name="dump_file" id="dump_file">
                        {each $dumpFile in $aDumpList}
                            <option value="{dumpFile}">{dumpFile}</option>
                        {/each}
                    </select>
                </label>
                <input type="submit" name="restore_dump" value="{lang 'Restore data from dump'}" /> &nbsp; &bull; &nbsp;
                <input type="submit" name="remove_dump" value="{lang 'Delete dump'}" />
            </td>
        </tr>


        <tr>
            <td class="bold">{lang 'Database Restore from PC'}</td>
        </tr>

        <tr>
            <td class="border vs_padd">
                <label for="sql_file">
                    {lang 'Please select a SQL file (extension ".sql" or compressed archive ".gz")'}
                </label>
                <input type="file" name="sql_file" id="sql_file" accept=".sql,.gz" class="center"/>
               <input type="submit" name="restore_sql_file" value="{lang 'Restore'}" />
            </td>
        </tr>
    </table>
</form>
