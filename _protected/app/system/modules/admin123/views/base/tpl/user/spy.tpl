<div class="center">
    {each $userSpy in $data}
        <div class="row">
            <div class="col-md-2">
                {lang 'Profile ID:'} <a href="{{ $design->url('cool-profile-page', 'main', 'index', $userSpy->profileId) }}">#{{ $userSpy->profileId }}</a>
            </div>
            <div class="col-md-4">
                {lang 'URL:'} <a href="{{ $userSpy->url }}">{{ $userSpy->url }}</a>
            </div>
            <div class="col-md-4">
                {lang 'User action:'} <a href="{{ $userSpy->userAction }}">{{ $userSpy->userAction }}</a>
            </div>
            <div class="col-md-2">
                {lang 'Date:'} <a href="{{ $userSpy->lastActivity }}">{{ $userSpy->lastActivity }}</a>
            </div>
        </div>
    {/each}
</div>

{main_include 'page_nav.inc.tpl'}
