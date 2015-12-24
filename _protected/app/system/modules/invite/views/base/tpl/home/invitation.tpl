<div class="left" id="block_page">
  <h1>{lang 'Invite your Friends'}</h1>
  {{ InviteForm::display() }}
</div>

<div class="right">
  <div class="ad_336_280">{{ $designModel->ad(336,280) }}</div>
  {{ $design->likeApi() }}
</div>
