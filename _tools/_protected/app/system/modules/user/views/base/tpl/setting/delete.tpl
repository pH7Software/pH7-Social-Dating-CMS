<div class="center">
    {if !$delete_status}
        <p class="bold green1">
            {lang 'Excellent choice!'}<br />
            {lang 'You will see, you will not regret it!'}<br />
            {lang 'At %site_name%, we work hard to give you one of the best social service!'}
        </p>
    {else}
        <p class="bold red">
            {lang 'Are you really sure you want to delete your account?'}
        </p>

        <ul>
            <li>
                <a class="bold" href="{{ $design->url('user','setting','delete','nodelete') }}">
                    {lang 'No, I changed my mind and want to stay with you! 🎉'}
                </a>
            </li>
            <li>
                <a href="{{ $design->url('user','setting','delete','yesdelete') }}">
                    {lang 'Yes, I really want to delete my account'}
                </a>
            </li>
        </ul>
    {/if}
</div>
