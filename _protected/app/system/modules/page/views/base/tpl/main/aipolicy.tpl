{* AI Policy Page Template *}
{include file="_header.tpl"}

{* Robots/meta tags for AI Policy: noindex, follow, and AI/LLM-specific meta if needed *}
{capture assign=extra_meta}
    <meta name="robots" content="noindex,follow">
    <meta name="llm-policy" content="see /ai-policy">
{/capture}
{$extra_meta}

<h1>{$h1_title}</h1>

<p>
    {t}This page describes our policy regarding the use of Artificial Intelligence (AI) and Large Language Models (LLMs) on {site_name}.{/t}
</p>

<h2>{t}AI/LLM Data Usage{/t}</h2>
<p>
    {t}We may use AI/LLM technologies to improve user experience, content moderation, and site features. Data processed by AI is handled in accordance with our privacy policy.{/t}
</p>

<h2>{t}Opt-Out{/t}</h2>
<p>
    {t}If you wish to opt out of AI/LLM data processing, please contact us at{/t} <a href="mailto:{$admin_email}">{$admin_email}</a>.
</p>

<h2>{t}More Information{/t}</h2>
<ul>
    <li><a href="{$url_root}privacy">{t}Privacy Policy{/t}</a></li>
    <li><a href="{$url_root}terms">{t}Terms of Service{/t}</a></li>
</ul>

{include file="_footer.tpl"}
