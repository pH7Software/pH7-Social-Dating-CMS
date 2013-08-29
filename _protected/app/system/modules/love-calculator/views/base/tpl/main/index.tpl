<table class="center">

  <tr>
    <td width="126" height="140">
      {{ $avatarDesign->get($username, $first_name, $sex, 200) }}
    </td>

    <td width="300">
      <div class="heart"><span>0</span>%</div>
      <p class="love_txt bold pink2">{lang 'Love!'}</p>
    </td>

    <td width="126" height="140">
      {{ $avatarDesign->get($second_username, $second_first_name, $second_sex, 200) }}
    </td>

  </tr>

</table>

<script>$(function(){ loveCounter(0, {amount_love}) });</script>
