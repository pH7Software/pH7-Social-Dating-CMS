<div class="col-md-8 col-lg-7">
    <p class="center">
        <a class="bold btn btn-default btn-md" href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'browse') }}">
            {lang 'Back to Browse Users'}
        </a>
    </p>

    {{ UpdateUserPassword::display($user_password) }}
</div>
