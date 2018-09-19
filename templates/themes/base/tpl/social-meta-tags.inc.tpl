<meta property="og:type" content="article" />
<meta property="og:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title), true) %} - {site_name}{else}{site_name} - {slogan}{/if}" />
<meta property="og:description" content="{% $str->escape($str->upperFirst($meta_description), true) %}" />
<meta property="og:url" content="{current_url}" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title), true) %} - {site_name}{else}{site_name} - {slogan}{/if}" />
<meta name="twitter:description" content="{% $str->escape($str->upperFirst($meta_description), true) %}" />
<meta name="twitter:url" content="{current_url}" />
