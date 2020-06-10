{* Set variables for TOS and Privacy links *}
{{ $terms_url = Framework\Mvc\Router\Uri::get('page', 'main', 'terms') }}
{{ $privacy_url = Framework\Mvc\Router\Uri::get('page', 'main', 'privacy') }}

<div id="disclaimer-dialog">
    <div class="center">
        <h1>
            {lang}Welcome to %site_name% ❤️{/lang}
        </h1>

        <p class="italic">
            {lang}This site contains sexually-oriented adult materials which may be offensive to some viewers.{/lang}
        </p>

        <p class="bold">
            {lang}To continue, please acknowledge and confirm you are over <span class="underline">18</span>.{/lang}
        </p>

        <p>
            <button id="agree-over18" class="btn btn-success btn-lg">
                {lang 'I am over 18'}
            </button>
            <button id="disagree-under18" class="btn btn-secondary btn-lg">
                {lang 'I am under 18'}
            </button>
        </p>

        <p>
            <small>
                {lang 'By entering to "%site_url%", you are agreeing to the <a href="%0%" target="_blank" rel="nofollow">Terms of Use</a> and <a href="%1%" target="_blank" rel="nofollow">Privacy Policy</a>.', $terms_url, $privacy_url}
            </small>
        </p>
    </div>
</div>
<div id="disclaimer-background"></div>
