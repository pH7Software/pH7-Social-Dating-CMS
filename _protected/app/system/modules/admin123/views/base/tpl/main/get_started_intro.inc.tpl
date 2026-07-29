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
                      {lang 'Add sample profiles while your community grows (avoids the "empty site" feel)'}
                  </a> 👥
              </li>
            {/if}

            <li>
                {{ $boxes = ['donationbox', 'upsetbox'] }}
                {{ $box = $boxes[mt_rand(0,1)] }}
                <a class="underline" href="{{ $design->url('ph7cms-helper', 'main', 'suggestionbox', '?box='.$box) }}">
                    {lang 'Contribute to the software'}
                </a> 🚀
            </li>

            <li>
                <a href="{tweet_msg_url}" target="_blank" rel="noopener noreferrer">
                    {lang 'Share it on Twitter'}
                </a> 💙
            </li>
        </ul>
    </div>
</div>
