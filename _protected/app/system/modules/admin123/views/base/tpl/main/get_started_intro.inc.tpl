<div class="center">
    <div class="border">
        <h2 class="underline">{lang 'Getting Started Smoothly'}</h2>

        <p>{lang 'Welcome to your admin dashboard! You will find everything you need to customize and manage your website in here 🙂'}</p>
        <p class="underline">{lang 'Below are a few steps to start well your webapp!'}</p>

        <ul>
            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=general">
                    {lang 'Review the general settings'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'metamain') }}">
                    {lang 'Update homepage texts & site info'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=icon">
                    {lang 'Upload site icon'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=design">
                    {lang "Update website's colors"}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=email">
                    {lang 'Update email settings'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'file', 'pagedisplay') }}">
                    {lang 'Edit static pages'}
                </a>
            </li>

            <li>
                <a class="underline" href="{patreon_url}" target="_blank" rel="noopener noreferrer">
                    {lang 'Become a Patron TODAY'}
                </a> 🚀
            </li>
        </ul>
    </div>
</div>
