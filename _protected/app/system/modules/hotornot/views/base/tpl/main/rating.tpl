<div class="center">

  <h2 class="pink1">{desc_for_woman}</h2>
  <h2 class="pink2">{desc_for_man}</h2>

  <div class="s_bMarg"></div>

  {if empty($error)}

    {{ $avatarDesign->get($data->username, $data->firstName, $data->sex, 400) }}
    <div class="hon_click">{{ RatingDesignCore::voting($data->profileId,'Members','center') }}</div>

    <br /><hr /><br />
    <p class="center">{{ $design->like($data->username, $data->firstName, $data->sex,(new UserCore)->getProfileLink($data->username)) }} | {{ $design->report($data->profileId, $data->username, $data->firstName, $data->sex) }}</p>
    {{ $design->likeApi() }}

  {else}

    <p class="bold">{error}</p>

  {/if}

</div>
