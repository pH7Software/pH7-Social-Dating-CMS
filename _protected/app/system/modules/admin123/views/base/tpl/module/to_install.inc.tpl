{if !$oModule->showAvailableMods(Module::INSTALL)}
    <h2 class="underline">{lang 'No module available in the repository for %software_name%'}</h2>
{else}
    <h2 class="underline">{lang 'Module available for %software_name%:'}</h2><br />

    <form method="post">
        {each $sFolder in $oModule->showAvailableMods(Module::INSTALL)}

            {{ $sModsDirModFolder = $oFile->checkExtDir($sFolder) }}

            {if $oModule->checkModFolder(Module::INSTALL, $sModsDirModFolder)}

                {{ $oModule->readConfig(Module::INSTALL, $sModsDirModFolder) }}

                <p class="underline italic"><a href="{% $config->values['module.information']['website'] %}" title="{lang 'Website of module'}">{% $config->values['module.information']['name'] %}</a> {lang 'version'} {% $config->values['module.information']['version'] %} {lang 'by'} <a href="mailto:{% $config->values['module.information']['email'] %}" title="{lang 'Contact Author'}">{% $config->values['module.information']['author'] %}</a></p>
                <button type="submit" class="success btn btn-default btn-tiny" name="submit_mod_install" value="{% $sModsDirModFolder %}" onclick="return confirm('{lang 'Are you sure to install this module?'}');">{lang 'Install'} {% $config->values['module.information']['name'] %}</button>

                <p><span class="bold">{lang 'Category:'}</span> <span class="italic">{% $config->values['module.information']['category'] %}</span></p>
                <p><span class="bold">{lang 'Description:'}</span> <span class="italic">{% $config->values['module.information']['description'] %}</span></p>
                <p><span class="bold">{lang 'License:'}</span> <span class="italic">{% $config->values['module.information']['license'] %}</span></p>

            {else}
                <button type="submit" class="error disabled btn btn-default btn-tiny" disabled="disabled">{lang 'Module path id not valid!'}</button><br />
            {/if}
            <hr /><br />
        {/each}
    </form>
{/if}
