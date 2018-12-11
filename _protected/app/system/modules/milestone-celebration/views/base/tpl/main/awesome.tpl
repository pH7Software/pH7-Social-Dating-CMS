<div class="center">
    <h2 class="underline">{message}</h2>

    <p class="bold italic">
        {lang "Let's celebrate it on"} <a href="{tweet_msg_url}" target="_blank" rel="noopener noreferrer">Twitter</a> 🎉
    </p>

    <p>
        <img src="{url_tpl_mod_img}smile.svg" alt="{lang 'So Happy!'}" height="250" />
    </p>

    <p>
        <a class="btn btn-primary" href="{{ $design->url('user', 'main', 'login') }}">
            <strong>{lang 'Sign In'}</strong>
        </a>
    </p>
</div>
