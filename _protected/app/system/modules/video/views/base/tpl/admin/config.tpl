<div class="col-md-8">
    {{ ConfigFileCoreForm::display('module.api') }}

    <p class="small">{lang}You need a Google API key to retrieve the Youtube video infos.{/lang}<br />
    {lang 'You can get one <a href="%0%">here</a>. Then, select "Api Key" for the Credential type, then select "browser API key", them you will get your API key to paste in this form. ', 'http://console.developers.google.com/apis/credentials?project=_'}</p>
</div>