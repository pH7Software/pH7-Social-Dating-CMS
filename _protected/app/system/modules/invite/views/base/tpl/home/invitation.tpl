<div class="left col-md-10" id="block_page">
  <h1>{lang 'Invite your Friends'}</h1>
  {{ InviteForm::display() }}
</div>

<div class="right col-md-2">
  <div class="ad_160_600">{{ $designModel->ad(160,600) }}</div>
  {{ $design->likeApi() }}
</div>
