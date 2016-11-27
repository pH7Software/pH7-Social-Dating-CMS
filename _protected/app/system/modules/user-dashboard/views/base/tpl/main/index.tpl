<div class="row">
    <div class="left col-md-3">
        <h3>{lang 'Your Profile Photo'}</h3>
        {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}
	    <p><a href="{{ $design->url('user','setting','avatar') }}">{lang 'Change My Profile Photo'}</a></p>
    </div>

    <div class="left col-md-6">
        <h3 class="center">{lang 'Your Dashboard'}</h3>

    </div>

    <div class="left col-md-3">
        <h3>{lang 'The latest news'}</h3>
        <div id="wall"></div>
    </div>
</div>
