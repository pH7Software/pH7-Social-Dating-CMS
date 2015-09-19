<div class="center">

  <div class="left">

    <div class="progress">
      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width:33%">33% - STEP 1/3</div>
    </div>

    {{ JoinForm::step1() }}
  </div>

  <div class="right animated fadeInRight">
    <p>{lang 'Already registered?'} <a href="{{ $design->url('user','main','login') }}"><strong>{lang 'Sign In!'}</strong></a></p>
    {if !empty($user_ref)}
      <a href="{{ $design->getUserAvatar($username, $sex, 400) }}" title="{first_name}" data-popup="image"><img class="avatar s_marg" alt="{first_name} {username}" title="{first_name}" src="{{ $design->getUserAvatar($username, $sex, 200) }}" /></a>
    {else}
      <br />
      {{ $userDesignModel->profilesBlock() }}
    {/if}
  </div>

</div>
