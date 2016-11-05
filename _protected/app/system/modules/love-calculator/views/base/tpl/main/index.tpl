<div class="calculator-wrapper">
    <div class="col-md-4">
      {{ $avatarDesign->get($username, $first_name, $sex, 200) }}
    </div>

    <div class="col-md-4">
      <div class="center heart"><span>0</span>%</div>
      <p class="love_txt bold pink2">{lang 'Love!'}</p>
    </div>

    <div class="col-md-4">
      {{ $avatarDesign->get($second_username, $second_first_name, $second_sex, 200) }}
    </div>
</div>

<script>$(function(){ loveCounter(0, {amount_love}) });</script>
