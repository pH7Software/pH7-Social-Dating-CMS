<div class="center">
    <h2 class="underline">{lang 'Getting Started'}</h2>
    <p>{lang 'Welcome to your admin dashboard! You will find everything you need to customize and manage your website in here 🙂'}</p>
    <p class="underline">{lang 'Below are a few steps to start well your webapp!'}</p>

    <ul>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=general">{lang 'General Useful Settings'}</a></li>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'metamain') }}">{lang 'Homepage texts and other site information'}</a></li>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=icon">{lang 'Icon Logo'}</a></li>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=design">{lang "Website's Color"}</a></li>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=email">{lang 'Email'}</a></li>
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'file', 'pagedisplay') }}">{lang 'Edit Static Pages'}</a></li>
        <li><a href="{patreon_url}" target="_blank" rel="noopener noreferrer">{lang 'Become a Patron TODAY'}</a> 🚀</li>
    </ul>
</div>
