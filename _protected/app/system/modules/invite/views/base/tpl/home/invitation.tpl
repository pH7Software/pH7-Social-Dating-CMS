<div class="left col-md-8">
    <div id="block_page">
        <h1>{lang 'Invite your Friends'}</h1>
        {{ InviteForm::display() }}
    </div>
</div>

<div class="right col-md-4">
    <div class="ad_336_280">{{ $designModel->ad(336,280) }}</div>
    {{ $design->likeApi() }}
</div>
