<div class="center">

{if empty($error)}

  {map}
  <br />
  {{ $userDesignModel->geoProfiles($country_code, $city, $first_user, $nb_user_by_page) }}
  <br /><br />

  {main_include 'page_nav.inc.tpl'}

{else}

  <p>{error}</p>

{/if}

</div>
