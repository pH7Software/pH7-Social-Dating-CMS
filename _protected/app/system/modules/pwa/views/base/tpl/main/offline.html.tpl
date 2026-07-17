<!DOCTYPE html>
<html lang="{% $config->values['language']['lang'] %}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{lang 'You are offline'} | {site_name}</title>
    {* Self-contained on purpose: this page is served from the service worker cache with no network *}
    <style>
        :root {
            --bg: #ffffff;
            --text: #1c1c26;
            --muted: #6b6b7a;
            color-scheme: light dark;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #15151c;
                --text: #ececf2;
                --muted: #a2a2b2;
            }
        }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }
        main {
            padding: 2em;
            max-width: 26em;
        }
        .heart {
            font-size: 3em;
        }
        h1 {
            font-size: 1.6em;
            letter-spacing: -.015em;
        }
        p {
            color: var(--muted);
            line-height: 1.5;
        }
        button {
            margin-top: 1.2em;
            padding: .7em 1.8em;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, #e8467c 0%, #7c5cff 100%);
            color: #fff;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            filter: brightness(1.08);
        }
    </style>
</head>
<body>
    <main>
        <div class="heart" role="img" aria-label="{lang 'Broken heart'}">💔</div>
        <h1>{lang 'You are offline'}</h1>
        <p>{lang "It looks like you've lost your Internet connection. %0% will be waiting for you as soon as you're back online.", '<strong>' . $site_name . '</strong>'}</p>
        <button onclick="location.reload()">{lang 'Try again'}</button>
    </main>
    <script>
        // Auto-reload as soon as connectivity returns
        window.addEventListener('online', function () {
            location.reload();
        });
    </script>
</body>
</html>
