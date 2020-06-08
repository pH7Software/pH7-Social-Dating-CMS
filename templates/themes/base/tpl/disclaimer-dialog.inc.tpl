{* Set variables for TOS and Privacy links *}
{{ $terms_url = Framework\Mvc\Router\Uri::get('page', 'main', 'terms') }}
{{ $privacy_url = Framework\Mvc\Router\Uri::get('page', 'main', 'privacy') }}

<div id="disclaimer-window" class="center">
    <h1>
        {lang}Welcome to %site_name% ❤️{/lang}
    </h1>

    <p class="italic s_tMarg">
        {lang}This site contains sexually-oriented adult materials which may be offensive to some viewers.{/lang}
    </p>

    <p class="bold s_tMarg">
        {lang}To continue, please acknowledge and confirm you are over <span class="underline">18</span>.{/lang}
    </p>

    <p class="s_tMarg">
        <button id="agree-over18" class="btn btn-success btn-lg">
            {lang 'I am over 18'}
        </button>
        <button id="disagree-under18" class="btn btn-secondary btn-lg">
            {lang 'I am under 18'}
        </button>
    </p>

    <p class="s_tMarg">
        <small>
            {lang 'By entering to to "%site_url%", you are agreeing to the <a href="%0%" target="_blank" rel="nofollow">Terms of Use</a> and <a href="%1%" target="_blank" rel="nofollow">Privacy Policy</a>.', $terms_url, $privacy_url}
        </small>
    </p>
</div>
<div id="disclaimer-background"></div>

{* Include the disclaimer's asset files *}
{{ $design->staticFiles('css', PH7_STATIC . PH7_CSS . PH7_JS, 'disclaimer.css') }}
{{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'disclaimer.js') }}
