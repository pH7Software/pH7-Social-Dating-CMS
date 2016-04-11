<div class="col-md-8">
    <p>{lang 'Membership Name:', $info->membershipName}</p>

    <p>{lang 'Expiration Date:'}
    {if $info->expirationDays == 0}
      {lang 'Unlimited'}
    {else}
      {% date_sub($info->membershipDate, date_interval_create_from_date_string($info->expirationDays . ' day')) %}
    {/if}
    </p>

    <p><a class="btn btn-primary btn-md" href="{{ $design->url('payment', 'main', 'membership') }}">{lang 'Renew your membership'}</a></p>
</div>