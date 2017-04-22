{if !$oModule->showAvailableMods(Module::UNINSTALL)}
    <h2 class="underline">{lang 'No modules available in your %software_name% repository'}</h2>
{else}
    <h2 class="underline">{lang 'Module(s) installed on your website:'}</h2><br />

    <form method="post">
        {each $sFolder in $oModule->showAvailableMods(Module::UNINSTALL)}
            {{ $sModsDirModFolder = $oFile->checkExtDir($sFolder) }}

            {if $oModule->checkModFolder(Module::UNINSTALL, $sModsDirModFolder)}
                {{ $oModule->readConfig(Module::UNINSTALL, $sModsDirModFolder) }}

                <p class="underline italic"><a href="{% $config->values['module.information']['website'] %}" title="{lang 'Website of module'}">{% $config->values['module.information']['name'] %}</a> {lang 'version'} {% $config->values['module.information']['version'] %} {lang 'by'} <a href="mailto:{% $config->values['module.information']['email'] %}" title="{lang 'Contact Author'}">{% $config->values['module.information']['author'] %}</a></p>
                <button type="submit" class="btn btn-default btn-md" name="submit_mod_uninstall" value="{% $sModsDirModFolder %}" onclick="return confirm('{lang 'Are you sure you want to uninstall this module?'}');">{lang 'Uninstall'} {% $config->values['module.information']['name'] %}</button><br />

            {else}
                <button type="submit" class="error disabled btn btn-default btn-md" disabled="disabled">{lang 'Module path id not valid!'}</button><br />
            {/if}
            <hr /><br />
        {/each}
    </form>
{/if}
