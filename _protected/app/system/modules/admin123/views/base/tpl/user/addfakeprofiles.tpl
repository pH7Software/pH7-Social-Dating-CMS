<div class="col-md-8">
    {{ AddFakeProfilesForm::display() }}

    <p class="small text-muted">
        {lang 'Profiles generated with <a href="%0%">%1%, %2%</a>', AddFakeProfilesFormProcess::SERVICE_URL, AddFakeProfilesFormProcess::SERVICE_NAME, AddFakeProfilesFormProcess::API_VER}
    </p>

    <p class="red">
        <small>
            {lang}If you generate too many profiles, you might see duplicated profiles, due to the fact they are generated randomly.{/lang}
        </small>
    </p>
</div>
