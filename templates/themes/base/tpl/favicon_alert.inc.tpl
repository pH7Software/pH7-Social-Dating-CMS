{* Favicon Alert *}

{* Initialize to 0 Alert *}
{{ $favicon_alert = 0 }}

{if !empty($count_unread_mail)}
  {{ $favicon_alert += $count_unread_mail }}
{/if}

{if !empty($count_pen_friend_request)}
  {{ $favicon_alert += $count_pen_friend_request }}
{/if}

{* Run Favicon Alert *}
{if $favicon_alert > 0}
  <script src="{url_static_js}tinycon.js"></script>
  <script>Tinycon.setBubble({favicon_alert})</script>
{/if}
