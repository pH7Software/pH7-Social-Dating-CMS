<div>
    <a class="s_tMarg btn btn-success" rel="nofollow" href="{{ $design->url('mail', 'main', 'compose', $data->username) }}">
        {lang 'Interested 👍'}
    </a>
    <a class="s_tMarg btn btn-danger" rel="nofollow" href="{{ $design->url('hotornot', 'main', 'rating') }}">
        {lang 'Not Interested 👎'}
    </a>

    <p class="s_tMarg italic small">
        {lang}If the photo does not match your sexual preference, please be respectful and press Not Interested{/lang}
    </p>
</div>
