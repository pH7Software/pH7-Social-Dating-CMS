<meta property="og:type" content="article" />
<meta property="og:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title)) %}{else}{site_name}{/if}" />
<meta property="og:description" content="{% $str->escape($str->upperFirst($meta_description)) %}" />
<meta property="og:url" content="{current_url}" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title)) %}{else}{site_name}{/if}" />
<meta name="twitter:description" content="{% $str->escape($str->upperFirst($meta_description)) %}" />
<meta name="twitter:url" content="{current_url}" />

{if !empty($image_social_meta_tag)}
    <meta name="thumbnail" content="{image_social_meta_tag}" />
    <meta name="twitter:image" content="{image_social_meta_tag}" />
    <meta property="og:image" content="{image_social_meta_tag}" />
{/if}
