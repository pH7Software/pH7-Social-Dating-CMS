<div class="right animated fadeInRight">
    <h1 class="pink2 italic underline">{lang 'Be on the best place to meet people!'}</h1>
    {{ JoinForm::step1(290) }}

    <div class="counter center">{{ $userDesign->counterUsers() }}</div>
</div>

<div class="left">
  <div class="folio_block">
    <h1 class="pink2 italic underline">{slogan}</h1>

    <div class="splash_slideshow">
      <div class="window">
        <div class="img_reel">
          <a href="{url_root}"><img src="{url_tpl_img}slideshow/1.jpg" alt="{lang 'Free Online Dating'}" /></a>
          <a href="{url_root}"><img src="{url_tpl_img}slideshow/2.jpg" alt="{lang 'Free Chat Rooms'}" /></a>
          <a href="{url_root}"><img src="{url_tpl_img}slideshow/3.jpg" alt="{lang 'Dating with Webcam Chat'}" /></a>
          <a href="{url_root}"><img src="{url_tpl_img}slideshow/4.jpg" alt="{lang 'Dating Flirt'}" /></a>
          <a href="{url_root}"><img src="{url_tpl_img}slideshow/5.jpg" alt="{lang 'Social Community'}" /></a>
        </div>
      </div>
      <div class="paging">
        <a href="#" rel="1">1</a>
        <a href="#" rel="2">2</a>
        <a href="#" rel="3">3</a>
        <a href="#" rel="4">4</a>
        <a href="#" rel="5">5</a>
      </div>
    </div>
  </div>

  <div class="block_txt">
    <h2>{lang 'Meet people in %0% with %site_name%!', $design->geoIp(false)}</h2>
    {promo_text}
  </div>

  <div class="carousel">{{ $userDesignModel->carouselProfiles() }}</div>
</div>
