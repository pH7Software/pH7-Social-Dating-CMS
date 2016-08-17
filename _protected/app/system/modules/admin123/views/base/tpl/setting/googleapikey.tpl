<div class="col-md-8">
    {{ ConfigFileCoreForm::display('service.api', PH7_PATH_APP_CONFIG) }}

    <p class="small">{lang}Google Maps might not be working if you are not using your API key. It is highly recommended to use one.{/lang}<br />
    {lang 'You can get one <a href="%0%">here</a>.Then, select "<strong>Google Maps JavaScript API</strong>" for "<em>Which API are you using</em>" and "<strong>Web browser (Javascript)</strong>" for "<em>Where will you be calling the API from</em>", then you will get your API key to paste in this form. ', 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&amp;keyType=CLIENT_SIDE&amp;reusekey=true'}</p>
</div>