{if !empty($img_background)}
    {main_include 'profile_background.inc.tpl'}
{/if}

<div class="col-xs-12 col-sm-12 col-md-11 col-lg-10 col-lg-offset-1">
  <div class="row" itemscope="itemscope" itemtype="http://schema.org/Person">
    <div class="col-xs-12 col-sm-4 col-md-3 animated fadeInLeftBig">
        {{ UserDesignCoreModel::userStatus($id) }}
        {{ (new AvatarDesignCore)->lightBox($username, $first_name, $sex, 400) }}
        <h3 itemprop="name">
            <span itemprop="name">{first_name}</span> {middle_name} <span itemprop="familyName">{last_name}</span>
            {if empty($last_name) OR empty($middle_name)}
                {* Display the username if middle or last name is empty *}
                <span itemprop="additionalName" class="italic">({username})</span>
            {/if}
            {{ $design->report($id, $username, $first_name, $sex) }}
        </h3>

        {manual_include 'profile_links.inc.tpl'}

        <p>
            <i class="fa fa-{sex}"></i>
            <span class="bold">{lang 'I am a:'}</span>
            <span class="italic">
                <a itemprop="gender" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&match_sex='.$sex) }}">
                    {lang $sex}
                </a>
            </span>
        </p>

        <div class="break"></div>

        {if !empty($match_sex)}
            <p>
                <i class="fa fa-{match_sex}"></i>
                <span class="bold">{lang 'Looking for a:'}</span>
                <span class="italic">
                    <a href="{{ $design->url('user','browse','index', '?country='.$country_code) }}{match_sex_search}">
                        {lang $match_sex}
                    </a>
                </span>
            </p>
            <div class="break"></div>
        {/if}

        {if !empty($age)}
            <p>
                <span class="bold">{lang 'Age:'}</span>
                <span class="italic">
                    <a itemprop="birthDate" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&age='.$birth_date) }}">
                        {age}
                    </a>
                    <span class="gray">({birth_date_formatted})</span>
                </span>
            </p>
            <div class="break"></div>
        {/if}

        {* Profile's Fields *}
        {each $key => $val in $fields}
            {if $key != 'description' AND $key != 'middleName' AND $key != 'punchline' AND !empty($val)}
                {{ $val = escape($val, true) }}

                {if stripos($key, 'height') !== false}
                    <p>
                        <span class="bold">{lang 'Height:'}</span>
                        <span class="italic">
                            <a itemprop="height" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&height='.$val) }}">
                                {{ (new Framework\Math\Measure\Height($val))->display(true) }}
                            </a>
                        </span>
                    </p>
                {elseif stripos($key, 'weight') !== false}
                    <p>
                        <span class="bold">{lang 'Weight:'}</span>
                        <span class="italic">
                            <a itemprop="weight" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&weight='.$val) }}">
                                {{ (new Framework\Math\Measure\Weight($val))->display(true) }}
                            </a>
                        </span>
                    </p>
                {elseif $key == 'country'}
                    <p>
                        <span class="bold">{lang 'Country:'}</span>
                        <span class="italic">
                            <a itemprop="nationality" href="{{ $design->url('user','browse','index', '?country='.$country_code) }}">
                                {country}
                            </a>
                        </span> <img src="{{ $design->getSmallFlagIcon($country_code) }}" title="{country}" alt="{country}" />
                    </p>
                {elseif $key == 'city'}
                    <p>
                        <span class="bold">{lang 'City/Town:'}</span>
                        <span class="italic">
                            <a itemprop="homeLocation" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&city='.$city) }}">
                                {city}
                            </a>
                        </span>
                    </p>
                {elseif $key == 'state'}
                    <p>
                        <span class="bold">{lang 'State/Province:'}</span>
                        <span class="italic">
                            <a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&state='.$state) }}">
                                {state}
                            </a>
                        </span>
                    </p>
                {elseif $key == 'zipCode'}
                    <p>
                        <span class="bold">{lang 'Postal Code:'}</span>
                        <span class="italic">
                            <a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&zip_code='.$val) }}">
                                {val}
                            </a>
                        </span>
                    </p>
                {elseif stripos($key, 'website') !== false}
                    <p>
                        {{ $design->favicon($val) }} <span class="bold">{lang 'Site/Blog:'}</span>
                        <span class="italic">{{ $design->urlTag($val) }}</span>
                    </p>
                {elseif stripos($key, 'socialNetworkSite') !== false}
                    <p>
                        {{ $design->favicon($val) }} <span class="bold">{lang 'Social Profile:'}</span>
                        <span class="italic">{{ $design->urlTag($val) }}</span>
                    </p>
                {else}
                    {{ $lang_key = strtolower($key) }}

                    {if strstr($key, 'url') OR stristr($val, 'http')}
                        <p>
                            {{ $design->favicon($val) }} <span class="bold">{lang $lang_key}</span>
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

        {{ $design->socialMediaWidgets() }}
    </div>

    <div class="col-xs-12 col-sm-6 col-md-6 animated fadeInDownBig">
        {if !empty($punchline)}
            <div class="profile-section">
                <h1 class="cinnabar-red italic center">{punchline}</h1>
            </div>
        {/if}

        {if !empty($description)}
            <div class="profile-section">
                <h2 class="center">{lang 'A Little About Me'}</h2>
                <div itemprop="description" class="quote italic center">{description}</div>
            </div>
        {/if}

        {if $is_picture_enabled}
            <h2 class="center">{lang 'Photos'}</h2>
            <div class="profile-section">
                <div class="content" id="picture">
                    <script>
                        var url_picture_block = '{{ $design->url('picture','main','albums',$username.'?show_add_album_btn='.((int)$is_own_profile)) }}';
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
                        var url_video_block = '{{ $design->url('video','main','albums',$username.'?show_add_album_btn='.((int)$is_own_profile)) }}';
                        $('#video').load(url_video_block + ' #video_block');
                    </script>
                </div>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_relatedprofile_enabled}
            <h2 class="center">{lang 'Similar Profiles'}</h2>
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

        {manual_include 'interested_or_not.buttons.inc.tpl'}
    </div>

    <div class="col-xs-12 col-sm-2 col-md-3 animated fadeInRightBig">
        {if $is_map_enabled}
            <div class="s_bMarg">
                <h2>{lang 'Location'}</h2>
                {map}
            </div>
        {/if}

        <div class="ad_160_600">
            {designModel.ad(160, 600)}
        </div>

        {{ CommentDesignCore::link($id, 'profile') }}
    </div>
  </div>
</div>
