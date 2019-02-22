{if !empty($img_background)}
    {* Set custom profile background (if set by user) *}
    {manual_include 'profile_background.inc.tpl'}
{/if}

<div class="row">
    <div class="col-xs-12 col-sm-4 col-md-3">
        {{ UserDesignCoreModel::userStatus($id) }}
        {{ (new AvatarDesignCore)->lightBox($username, $first_name, $sex, 400) }}
        <h3>{first_name} {middle_name} {last_name}
            {if empty($last_name) OR empty($middle_name)}
                {* show username if middle or last name isn't set *}
                <span class="italic">({username})</span>
            {/if}
            {{ $design->report($id, $username, $first_name, $sex) }}
        </h3>

        {manual_include 'profile_links.inc.tpl'}

        {* Profile's Fields *}
        {each $key => $val in $fields}
            {if $key != 'description' AND $key != 'middleName' AND $key != 'punchline' AND !empty($val)}
                {{ $val = escape($val, true) }}

                {if $key == 'city'}
                    <p>
                        <span class="bold">{lang 'City/Town:'}</span>
                        <span class="italic">
                            {city}
                        </span>
                    </p>
                {elseif $key == 'state'}
                    <p>
                        <span class="bold">{lang 'State/Province:'}</span>
                        <span class="italic">
                            {state}
                        </span>
                    </p>
                {elseif $key == 'zipCode'}
                    <p>
                        <span class="bold">{lang 'Postal Code:'}</span>
                        <span class="italic">
                            {val}
                        </span>
                    </p>
                {elseif $key == 'propertyPrice'}
                    <p>
                        <span class="bold">{lang 'Price:'}</span>
                        <span class="italic">
                            {val}
                        </span>
                    </p>
                {elseif $key == 'propertyBedrooms'}
                    <p>
                        <span class="bold">{lang 'Bedrooms:'}</span>
                        <span class="italic">
                            {val}
                        </span>
                    </p>
                {elseif $key == 'propertyBathrooms'}
                    <p>
                        <span class="bold">{lang 'Bedrooms:'}</span>
                        <span class="italic">
                            {val}
                        </span>
                    </p>
                {elseif $key == 'propertySize'}
                    <p>
                        <span class="bold">{lang 'Size:'}</span>
                        <span class="italic">
                            {val}
                        </span>
                    </p>
                {elseif $key == 'contactTimes'}
                    <p>
                        <span class="bold">{lang 'Best Time to Contact:'}</span>
                        <span class="italic">
                            {if $val === 'morning'}
                                {lang 'Morning (8.30am - 1pm)'}
                            {elseif $val === 'afternoon'}
                                {lang 'Afternoon (1pm - 5.30pm)'}
                            {elseif $val === 'evening'}
                                {lang 'Evening (5.30pm - 9pm)'}
                            {else}
                                {lang 'At anytime'}
                            {/if}
                        </span>
                    </p>
                {elseif $key == 'website'}
                    <p>
                        {{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang 'Site/Blog:'}</span>
                        <span class="italic">{{ $design->urlTag($val) }}</span>
                    </p>
                {else}
                    {{ $lang_key = strtolower($key) }}

                    {if strstr($key, 'url')}
                        <p>
                            {{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang $lang_key}</span>
                            <span class="italic">{{ $design->urlTag($val) }}</span>
                        </p>
                    {else}
                        <p>
                            <span class="bold">{lang $lang_key}</span>
                            <span class="italic">{val}</span>
                        </p>
                    {/if}
                {/if}
                <div class="break"></div>
            {/if}
        {/each}

        {{ $design->likeApi() }}
    </div>

    <div class="col-xs-12 col-sm-6 col-md-6">
        {if !empty($punchline)}
            <div class="profile-section">
                <h1 class="cinnabar-red italic center">{punchline}</h1>
            </div>
        {/if}

        {if !empty($description)}
            <div class="profile-section">
                <h2 class="center">{lang 'A Little About Me'}</h2>
                <div class="quote italic center">{description}</div>
            </div>
        {/if}

        {if $is_picture_enabled}
            <h2 class="center">{lang 'Photos'}</h2>
            <div class="profile-section">
                <div class="content" id="picture">
                    <script>
                        var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
                        $('#picture').load(url_picture_block + ' #picture_block');
                    </script>
                </div>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_video_enabled}
            <h2 class="center">{lang 'Videos'}</h2>
            <div class="profile-section">
                <div class="content" id="video">
                    <script>
                        var url_video_block = '{{ $design->url('video','main','albums',$username) }}';
                        $('#video').load(url_video_block + ' #video_block');
                    </script>
                </div>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_relatedprofile_enabled}
            <div class="profile-section">
                <div class="content" id="related_profile">
                    <script>
                        var url_related_profile_block = '{{ $design->url('related-profile','main','index',$id) }}';
                        $('#related_profile').load(url_related_profile_block + ' #related_profile_block');
                    </script>
                </div>
            </div>
            <div class="clear"></div>
        {/if}

        <div class="center small">
            {if !empty($join_date)}
                {lang 'Join Date:'} <span class="italic">{join_date}</span> •
            {/if}

            {if !empty($last_activity)}
                {lang 'Last Activity:'} <span class="italic">{last_activity}</span> •
            {/if}

            {lang 'Views:'}
            <span class="italic">
                {% Framework\Mvc\Model\Statistic::getView($id,DbTableName::MEMBER) %}
            </span>
        </div>

        <p class="center">
            {{ $design->like($username, $first_name, $sex) }}
        </p>

        {if $sex === 'seller'}
            <div class="center">
                <a class="s_tMarg btn btn-success btn-lg" rel="nofollow" href="{mail_link}">
                    {lang 'Interested 👍'}
                </a>
                <a class="s_tMarg btn btn-danger btn-lg" href="{{ $design->url('realestate', 'browse', 'seller', '?city='.$city) }}">
                    {lang 'Not Interested 👎'}
                </a>
            </div>
        {/if}
    </div>

    <div class="col-xs-12 col-sm-2 col-md-3">
        <div class="s_bMarg">
            <h2>{lang 'Location'}</h2>
            {map}
        </div>

        <div class="ad_160_600">
            {designModel.ad(160, 600)}
        </div>

        {{ CommentDesignCore::link($id, 'profile') }}
    </div>
</div>

{* Signup Popup *}
{if !$is_logged AND !AdminCore::auth()}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . 'realestate' . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'signup_popup.js') }}
{/if}
