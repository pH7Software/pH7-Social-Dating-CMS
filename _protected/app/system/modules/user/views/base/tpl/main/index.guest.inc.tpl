<div class="right">
  <h1 class="pink2 italic underline">{lang 'Welcome to %site_name%!'}</h1>

  {{ JoinForm::step1(290) }}
  <div class="counter center">{{ $userDesign->counterUsers() }}</div>
</div>

<div class="left">
  <div class="folio_block">
    <h1 class="pink2 italic underline">{lang 'Free Online Dating Social Community Site with Chat Rooms'}</h1>

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
    <h2>{lang 'Meet new people in'} {{ $design->geoIp() }} {lang 'with %site_name%'}</h2>
    <p>{lang 'This is the best place for meeting new people nearby. Chat, flirt, socialize and have fun!'}<br />
    {slogan}</p>
  </div>

  <div class="carousel">{{ $userDesignModel->carouselProfiles() }}</div>
</div>
