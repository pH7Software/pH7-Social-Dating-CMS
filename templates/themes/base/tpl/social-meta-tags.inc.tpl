<meta property="og:type" content="article" />
<meta property="og:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title), true) %} • {/if}{site_name}" />
<meta property="og:description" content="{% $str->escape($str->upperFirst($meta_description), true) %}" />
<meta property="og:url" content="{current_url}" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{if $page_title}{% $str->escape($str->upperFirst($page_title), true) %} • {/if}{site_name}" />
<meta name="twitter:description" content="{% $str->escape($str->upperFirst($meta_description), true) %}" />
<meta name="twitter:url" content="{current_url}" />
