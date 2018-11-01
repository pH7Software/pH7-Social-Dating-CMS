<div class="center">
    <div class="col-md-8 col-md-offset-2">
        <ul class="list-group">
            <li class="list-group-item bold">
                {lang 'Membership Name: %0%', '<span class="italic text-info">' . $membershipName . '</span>'}
            </li>
            <li class="list-group-item bold">
                {lang 'Expiration: %0%', '<span class="italic text-danger">' . $expirationDate . '</span>'}
            </li>
        </ul>
        <p>
            <a class="btn btn-primary btn-md" href="{{ $design->url('payment', 'main', 'membership') }}">
                {lang 'Renew/Upgrade your Membership'}
            </a>
        </p>
    </div>
</div>
