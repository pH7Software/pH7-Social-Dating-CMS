<div class="col-md-8">
    {{ ConfigFileCoreForm::display('module.api') }}

    <p class="small">
        {lang}You need a Google API key to retrieve the Youtube video infos.{/lang}<br />
        {lang 'You can get one <a href="%0%">here</a>. Enable YouTube Data API (which is disabled by default). Finally create an API key. Select "Api Key" for the Credential type, then select "Server Key", then you will get your API key to paste in this form. ', 'https://console.developers.google.com/apis/library'}
    </p>
</div>
