# 🎨 pH7Tpl 🖌️

## 🔭 Summary

**[pH7](https://github.com/pH-7)'s Template Engine** gives a readable syntax inspired from Smarty and some random language syntaxes I have seen during my degree in 2009.

**pH7Tpl** has been built to give as much freedom as possible for either developers or designers.


## 📖 The Syntaxes

pH7Tpl supports two different syntaxes:

1. "*Curly*" looks slightly similar to Smarty syntax.
2. "*TAL (Template Attribute Language)*" is inspired from XSLT/XML syntax, but simplified and easier to use.


## Syntax Rules

### Variable Output
- Use `{var}` to output a variable (no `$`, no curly braces inside):
  - Example: `{site_name}`
- Use `{% $var %}` to echo PHP values or expressions.
- Use `{{ $statement }}` for PHP statements that should not echo output.
- Do not use `{$var}`. This is Smarty-style syntax and is not supported.

### Translation (Internationalization)
- Use `{lang 'Short phrase'}` for short phrases or single words.
  - Example: `{lang 'Login'}`
- Use `{lang}Longer sentences or paragraphs{/lang}` for longer text blocks.
  - Example: `{lang}This is a full sentence or paragraph for translation.{/lang}`
- You can nest `{lang}` inside HTML tags, but do not nest `{lang}` blocks inside each other.
- Do NOT use `{t}` or `{_}` (not supported).

### Conditionals
- `{if condition}` ... `{else}` ... `{/if}`
  - Example: `{if is_logged_in}Welcome, {username}!{else}Please log in.{/if}`

### Loops
- `{foreach $array as $item}` ... `{/foreach}`
  - Example: `{foreach $users as $user}{user}{/foreach}`

### Includes
- Use `{main_include 'file.tpl'}` for files from the active theme.
- Use `{manual_include 'file.tpl'}` for module view files.
- Use `{def_main_include 'file.tpl'}` to include a file from the default theme.

### Comments
- `{* This is a comment *}`



### Best Practices for URLs
- All variables are referenced as `{var}` (no `$`).
- For dynamic URLs, assign them in your controller using `Uri::get()`, for example:
  - `$this->view->url_privacy = \PH7\Framework\Mvc\Router\Uri::get('page', 'main', 'privacy');`
  - `$this->view->url_terms = \PH7\Framework\Mvc\Router\Uri::get('page', 'main', 'terms');`
- Reference these variables in your template as `{url_privacy}` and `{url_terms}`.
- Keep URL building in controllers when the URL is page-specific. Use `{{ $design->url(...) }}` only for global layout concerns where the `Design` object is already assigned.
- Only use variables you have explicitly assigned in your controller for URLs. Variables like `{url_terms}` and `{url_privacy}` are NOT predefined and must be set manually.
- pH7Tpl is not Smarty: avoid Smarty-specific syntax.
- For translation, always use `{lang}` as shown above.

---

_This section documents the supported pH7Tpl syntax and avoids confusion with other template engines._


## 💨 Built to be the FASTEST 🌠

It translates all high-level syntax code into raw PHP, then saves it until the .tpl file is modified again (which will trigger again the language parsing cycle).

When the tpl "view" files are compiled into PHP code, pH7Tpl also optimizes the PHP code, removes extra open/close PHPtags, strips all comments and compacts the code.

Finally, pH7Tpl gives an output static cache for non-dynamic pages.

In short, without template engines (with only raw PHP code as template language), your website would be even slower than using pH7Tpl.


## 👨‍🎨 Author

Designed and Created by [Pierre-Henry Soria](https://pierrehenry.be).


## 🔢 Version

`v1.6.0`


## ⚖️ License

[Creative Commons Attribution, 3.0](http://creativecommons.org/licenses/by/3.0/)
