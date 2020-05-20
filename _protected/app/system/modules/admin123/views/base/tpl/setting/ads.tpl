<div class="col-md-10">
    {{ UpdateAdsForm::display() }}
    <br />
    {{ $sSlug = (AdsCore::getTable() === AdsCore::AFFILIATE_AD_TABLE_NAME) ? 'affiliate' : '' }}

    <p>
        <a class="bold btn btn-default btn-md" href="{{ $design->url(PH7_ADMIN_MOD, 'setting', 'addads', $sSlug) }}">
            {lang 'Add a new banner'}
        </a>
    </p>
    <br />
    {main_include 'page_nav.inc.tpl'}
</div>
