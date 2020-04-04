<h2>{lang 'Hi <em>%0%</em>! How are you today?', $first_name}</h2>
<h3 class="s_bMarg">{lang}Say hi to the new people and meet them!{/lang}</h3>
<h5 class="underline vs_marg">
    {lang 'Wall'} <span class="italic">&quot;{lang 'The latest news'}&quot;</span>
</h5>

<div class="left col-md-7" id="wall"></div>

<div class="right col-md-5">
    {{ $userDesignModel->profilesBlock() }}
</div>
