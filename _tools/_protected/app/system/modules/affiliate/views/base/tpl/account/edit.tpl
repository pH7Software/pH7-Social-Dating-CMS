<ol id="toc">
    <li>
        <a href="#general">
            <span>{lang 'General Info'}</span>
        </a>
    </li>
    <li>
        <a href="#bank">
            <span>{lang 'Bank Info'}</span>
        </a>
    </li>
</ol>

<div class="content" id="general">
    {manual_include 'edit.inc.tpl'}
</div>

<div class="content" id="bank">
    {manual_include 'bank.inc.tpl'}
</div>

<script src="{url_static}{% PH7_JS %}tabs.js"></script>
<script>tabs('p', ['general', 'bank']);</script>
