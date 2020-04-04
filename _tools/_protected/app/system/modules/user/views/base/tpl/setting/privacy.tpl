<div class="col-md-10">
    {{ PrivacyForm::display() }}

    <p class="s_tMarg small">
        <a href="{{ $design->url('user','setting','delete') }}">
            {lang 'Want to delete your account...?'}
        </a>
    </p>
</div>
