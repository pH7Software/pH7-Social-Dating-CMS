<div class="hon_actions" role="group" aria-label="{lang 'Profile actions'}">
    <a id="hotornot_skip" class="s_tMarg btn btn-danger" rel="nofollow" aria-keyshortcuts="ArrowLeft" href="{{ $design->url('hotornot', 'main', 'rating') }}">
        {lang '← Not Interested 👎'}
    </a>
    <a id="hotornot_interested" class="s_tMarg btn btn-success" rel="nofollow" aria-keyshortcuts="ArrowRight" href="{{ $design->url('mail', 'main', 'compose', $data->username) }}">
        {lang 'Interested 👍 →'}
    </a>
</div>
<p class="small center">{lang 'Tip: use the left and right arrow keys to decide quickly.'}</p>
