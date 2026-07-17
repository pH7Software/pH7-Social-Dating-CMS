{
  "name": {% json_encode($site_name, JSON_UNESCAPED_SLASHES) %},
  "short_name": {% json_encode($site_name, JSON_UNESCAPED_SLASHES) %},
  "id": {% json_encode(PH7_RELATIVE, JSON_UNESCAPED_SLASHES) %},
  "description": {% json_encode($meta_description, JSON_UNESCAPED_SLASHES) %},
  "dir": "ltr",
  "lang": {% json_encode($config->values['language']['lang'], JSON_UNESCAPED_SLASHES) %},
  "start_url": {% json_encode(PH7_RELATIVE, JSON_UNESCAPED_SLASHES) %},
  "homepage_url": {% json_encode(PH7_URL_ROOT, JSON_UNESCAPED_SLASHES) %},
  "orientation": {% json_encode($orientation, JSON_UNESCAPED_SLASHES) %},
  "scope": {% json_encode(PH7_RELATIVE, JSON_UNESCAPED_SLASHES) %},
  "theme_color": {% json_encode($hex_bg_color, JSON_UNESCAPED_SLASHES) %},
  "background_color": {% json_encode($hex_bg_color, JSON_UNESCAPED_SLASHES) %},
  "display": "standalone",
  "display_override": ["standalone", "minimal-ui"],
  "categories": ["social", "lifestyle"],
  "launch_handler": {
    "client_mode": "navigate-existing"
  },
  "shortcuts": [
    {
      "name": "Browse Members",
      "url": {% json_encode($browse_members_url, JSON_UNESCAPED_SLASHES) %},
      "icons": [{ "src": "{url_tpl_mod_img}icon-96x96.png", "sizes": "96x96", "type": "image/png" }]
    },
    {
      "name": "Messages",
      "url": {% json_encode($inbox_url, JSON_UNESCAPED_SLASHES) %},
      "icons": [{ "src": "{url_tpl_mod_img}icon-96x96.png", "sizes": "96x96", "type": "image/png" }]
    },
    {
      "name": "My Account",
      "url": {% json_encode($my_account_url, JSON_UNESCAPED_SLASHES) %},
      "icons": [{ "src": "{url_tpl_mod_img}icon-96x96.png", "sizes": "96x96", "type": "image/png" }]
    }
  ],
  "icons": [
    {
      "src": "{url_tpl_mod_img}icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-96x96.png",
      "sizes": "96x96",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-128x128.png",
      "sizes": "128x128",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-144x144.png",
      "sizes": "144x144",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-152x152.png",
      "sizes": "152x152",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-384x384.png",
      "sizes": "384x384",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    },
    {
      "src": "{url_tpl_mod_img}icon-maskable-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "maskable"
    }
  ]
}
