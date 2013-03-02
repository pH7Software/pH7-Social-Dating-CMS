{@if(!$oModule->showAvailableMods(Module::UNINSTALL))@}

  <h2 class="underline">{@lang('No module available in the repository for %software_name%')@}</h2>

{@else@}

  <h2 class="underline">{@lang('Module installed on the %software_name% software:')@}</h2><br />

  <form method="post">

  {@foreach($oModule->showAvailableMods(Module::UNINSTALL) as $sFolder)@}

         {{ $sModulesDirModuleFolder = $oFile->checkExtDir($sFolder) }}

         {@if($oModule->checkModFolder(Module::UNINSTALL, $sModulesDirModuleFolder))@}

             {{ $oModule->readConfig(Module::UNINSTALL, $sModulesDirModuleFolder) }}

             <p class="underline italic"><a href="{% $config->values['module.information']['website'] %}" title="{@lang('Website of module')@}">{% $config->values['module.information']['name'] %}</a> {@lang('version')@} {% $config->values['module.information']['version'] %} {@lang('by')@} <a href="mailto:{% $config->values['module.information']['email'] %}" title="{@lang('Contact Author')@}">{% $config->values['module.information']['author'] %}</a></p>
             <button type="submit" class="success" name="submit_mod_uninstall" value="{% $sModulesDirModuleFolder %}" onclick="return confirm('{@lang('Are you sure you want to uninstall this module?')@}');">{@lang('Uninstall')@} {% $config->values['module.information']['name'] %}</button><br />

         {@else@}

             <button type="submit" class="error" disabled="disabled">{@lang('Module path id not valid!')@}</button><br />

         {@/if@}

         <hr /><br />

  {@/foreach@}

  </form>

{@/if@}
