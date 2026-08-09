<div class="center">
    <div class="s_bMarg border">
        <h2>🍰 <span class="underline">{lang 'Getting Started'}</span> 👌</h2>

        <p>
            {lang 'Welcome to your admin dashboard! You will find everything you need to customize and manage your website in here 🙂'}
        </p>

        <p class="underline">
            {lang 'Here are a few steps to start well your site:'}
        </p>

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
                    {lang "Upload website's icon"}
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
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'module', 'disable') }}">
                    {lang 'Enable/disable modules'}
                </a>
            </li>

            {if $is_profile_faker_enabled}
              <li>
                  <a href="{{ $design->url('profile-faker', 'generator', 'addmember') }}">
                      {lang 'Add clearly labelled sample profiles on a non-production site'}
                  </a> 👥
              </li>
            {/if}

            <li>
                <a href="{{ $design->url('payment', 'admin', 'membershiplist') }}">
                    {lang 'Review membership permissions, prices, and expiry periods'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=automation">
                    {lang 'Configure the cron secret and server scheduler'}
                </a>
            </li>

            <li>
                <a href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'general') }}#p=registration">
                    {lang 'Review registration, activation, moderation, and anti-spam settings'}
                </a>
            </li>
        </ul>
    </div>
</div>
