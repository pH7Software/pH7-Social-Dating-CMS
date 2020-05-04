{{ $social_meta_title = $page_title ? $str->escape($str->upperFirst($page_title)) : $site_name }}
{{ $social_meta_desc = $str->escape($str->upperFirst($meta_description)) }}

<meta property="og:locale" content="{% PH7_LANG_NAME %}" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{social_meta_title}" />
<meta property="og:description" content="{social_meta_desc}" />
<meta property="og:url" content="{current_url}" />
<meta property="og:site_name" content="{site_name}" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{social_meta_title}" />
<meta name="twitter:description" content="{social_meta_desc}" />
<meta name="twitter:url" content="{current_url}" />

<meta itemprop="name" content="{social_meta_title}" />
<meta itemprop="description" content="{social_meta_desc}" />

{if !empty($image_social_meta_tag)}
    <meta name="thumbnail" content="{image_social_meta_tag}" />
    <meta name="twitter:image" content="{image_social_meta_tag}" />
    <meta property="og:image" content="{image_social_meta_tag}" />
    <meta itemprop="image" content="{image_social_meta_tag}" />
{/if}
