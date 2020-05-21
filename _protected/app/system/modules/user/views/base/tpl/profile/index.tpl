{if !empty($img_background)}
    {main_include 'profile_background.inc.tpl'}
{/if}

{if empty($error)}
    <ol id="toc">
        <li>
            <a href="#general">
                <span>{lang 'Info'}</span>
            </a>
        </li>

        {if $is_map_enabled}
            <li>
                <a href="#map">
                    <span>{lang 'Map'}</span>
                </a>
            </li>
        {/if}

        {if $is_relatedprofile_enabled}
            <li>
                <a href="#related_profile">
                    <span>{lang 'Similar Profiles'}</span>
                </a>
            </li>
        {/if}

        {if $is_friend_enabled}
            <li>
                <a href="#friend">
                    <span>{friend_link_name}</span>
                </a>
            </li>

            {if $is_logged AND !$is_own_profile}
                <li>
                    <a href="#mutual_friend">
                        <span>{mutual_friend_link_name}</span>
                    </a>
                </li>
            {/if}
        {/if}

        {if $is_picture_enabled}
            <li>
                <a href="#picture">
                    <span>{lang 'Photos'}</span>
                </a>
            </li>
        {/if}

        {if $is_video_enabled}
            <li>
                <a href="#video">
                    <span>{lang 'Videos'}</span>
                </a>
            </li>
        {/if}

        {if $is_forum_enabled}
            <li>
              <a href="#forum">
                  <span>{lang 'Topics'}</span>
              </a>
            </li>
        {/if}

        {if $is_note_enabled}
            <li>
                <a href="#note">
                    <span>{lang 'Notes'}</span>
                </a>
            </li>
        {/if}

        <li>
            <a href="#visitor">
                <span>{lang 'Recently Viewed'}</span>
            </a>
        </li>

        {if $is_mail_enabled AND $is_logged AND !$is_own_profile}
            <li>
                <a rel="nofollow" href="{mail_link}">
                    <span>{lang 'Message'}</span>
                </a>
            </li>
        {/if}

        {if $is_im_enabled AND $is_logged AND !$is_own_profile}
            <li>
                <a rel="nofollow" href="{messenger_link}">
                    <span>{lang 'Live Chat'}</span>
                </a>
            </li>
        {/if}

        {if $is_friend_enabled AND $is_logged AND !$is_own_profile}
            <li>
                <a rel="nofollow" href="{friend_link}">
                    <span>
                        {if $is_approved_friend}
                            {lang 'Remove Friend'}
                        {elseif $is_pending_friend}
                            {lang 'Manage Friend'}
                        {else}
                            {lang 'Add Friend'}
                        {/if}
                    </span>
                </a>
            </li>
        {/if}

        {if $is_lovecalculator_enabled AND $is_logged AND !$is_own_profile}
            <li>
                <a href="{{ $design->url('love-calculator','main','index',$username) }}" title="{lang 'Love Calculator'}">
                    <span>{lang 'Match'} <b class="pink2">&hearts;</b></span>
                </a>
            </li>
        {/if}
    </ol>

    <div class="content" id="general" itemscope="itemscope" itemtype="http://schema.org/Person">
        {{ UserDesignCoreModel::userStatus($id) }}
        {{ (new AvatarDesignCore)->lightBox($username, $first_name, $sex, 400) }}

        <p>
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
                <span class="bold">{lang 'Looking for a:'}</span>
                <span class="italic">
                    <a href="{{ $design->url('user','browse','index', '?country='.$country_code) }}{match_sex_search}">
                        {lang $match_sex}
                    </a>
                </span>
            </p>
            <div class="break"></div>
        {/if}

        <p>
            <span class="bold">{lang 'First name:'}</span>
            <span class="italic">
                <a itemprop="name" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&first_name='.$first_name) }}">
                    {first_name}
                </a>
            </span>
        </p>
        <div class="break"></div>

        {if !empty($middle_name)}
            <p>
                <span class="bold">{lang 'Middle name:'}</span>
                <span class="italic">
                    <a itemprop="additionalName" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&middle_name='.$middle_name) }}">
                        {middle_name}
                    </a>
                </span>
            </p>
            <div class="break"></div>
        {/if}

        {if !empty($last_name)}
            <p>
                <span class="bold">{lang 'Last name:'}</span>
                <span class="italic">
                    <a itemprop="familyName" href="{{ $design->url('user','browse','index', '?country='.$country_code.'&last_name='.$last_name) }}">
                        {last_name}
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
                        <span itemprop="url" class="italic">{{ $design->urlTag($val) }}</span>
                    </p>
                {elseif stripos($key, 'socialNetworkSite') !== false}
                    <p>
                        {{ $design->favicon($val) }} <span class="bold">{lang 'Social Profile:'}</span>
                        <span itemprop="url" class="italic">{{ $design->urlTag($val) }}</span>
                    </p>
                {else}
                    {{ $lang_key = strtolower($key) }}

                     {if strstr($key, 'url') OR stristr($val, 'http')}
                         <p>
                             {{ $design->favicon($val) }} <span class="bold">{lang $lang_key}</span>
                             <span itemprop="url" class="italic">{{ $design->urlTag($val) }}</span>
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

        {if !empty($join_date)}
            <p>
                <span class="bold">{lang 'Join Date:'}</span>
                <span class="italic">{join_date}</span>
            </p>
            <div class="break"></div>
        {/if}

        {if !empty($last_activity)}
            <p>
                <span class="bold">{lang 'Last Activity:'}</span>
                <span class="italic">{last_activity}</span>
            </p>
            <div class="break"></div>
        {/if}

        <p>
            <span class="bold">{lang 'Views:'}</span>
            <span class="italic">
                {% Framework\Mvc\Model\Statistic::getView($id,DbTableName::MEMBER) %}
            </span>
        </p>
        <div class="break"></div>

        {{ RatingDesignCore::voting($id,DbTableName::MEMBER) }}

        <div class="profile_desc">
            {if !empty($punchline)}
                <div class="punchline">{punchline}</div>
            {/if}

            {if !empty($description)}
                <div itemprop="description" class="quote italic">{description}</div>
                <div class="ad_336_280">
                    {designModel.ad(336, 280)}
                </div>
            {/if}
        </div>
    </div>

    {if $is_map_enabled}
        <div class="content" id="map">
            <span class="bold">{lang 'Profile Map:'}</span>{map}
        </div>
    {/if}

    {if $is_relatedprofile_enabled}
        <div class="content" id="related_profile">
        <script>
            var url_related_profile_block = '{{ $design->url('related-profile','main','index',$id) }}';
            $('#related_profile').load(url_related_profile_block + ' #related_profile_block');
        </script>
        </div>
    {/if}

    {if $is_friend_enabled}
        <div class="content" id="friend">
            <script>
                var url_friend_block = '{{ $design->url('friend','main','index',$username) }}';
                $('#friend').load(url_friend_block + ' #friend_block');
            </script>
        </div>
    {/if}

    {if $is_friend_enabled AND $is_logged AND !$is_own_profile}
        <div class="content" id="mutual_friend">
            <script>
                var url_mutual_friend_block = '{{ $design->url('friend','main','mutual',$username) }}';
                $('#mutual_friend').load(url_mutual_friend_block + ' #friend_block');
            </script>
        </div>
    {/if}

    {if $is_picture_enabled}
        <div class="content" id="picture">
            <script>
                var url_picture_block = '{{ $design->url('picture','main','albums',$username.'?show_add_album_btn='.((int)$is_own_profile)) }}';
                $('#picture').load(url_picture_block + ' #picture_block');
            </script>
        </div>
    {/if}

    {if $is_video_enabled}
        <div class="content" id="video">
            <script>
                var url_video_block = '{{ $design->url('video','main','albums',$username.'?show_add_album_btn='.((int)$is_own_profile)) }}';
                $('#video').load(url_video_block + ' #video_block');
            </script>
        </div>
    {/if}

    {if $is_forum_enabled}
        <div class="content" id="forum">
            <script>
                var url_forum_block = '{{ $design->url('forum','forum','showpostbyprofile',$username) }}';
                $('#forum').load(url_forum_block + ' #forum_block');
            </script>
        </div>
    {/if}

    {if $is_note_enabled}
        <div class="content" id="note">
            <script>
                var url_note_block = '{{ $design->url('note','main','author',$username) }}';
                $('#note').load(url_note_block + ' #note_block');
            </script>
        </div>
    {/if}

    <div class="content" id="visitor">
        <script>
            var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
            $('#visitor').load(url_visitor_block + ' #visitor_block');
        </script>
    </div>

    <div class="clear"></div>
    <p class="center">
        {{ $design->like($username, $first_name, $sex) }} | {{ $design->report($id, $username, $first_name, $sex) }}
    </p>
    {{ $design->socialMediaWidgets() }}

    {{ CommentDesignCore::link($id, 'profile') }}

    {* Setup the profile tabs *}
    <script src="{url_static_js}tabs.js"></script>
    <script>
        tabs('p', [
            'general',
            {if $is_map_enabled}'map',{/if}
            {if $is_relatedprofile_enabled}'related_profile',{/if}
            {if $is_friend_enabled}
                'friend',
                {if $is_logged AND !$is_own_profile}'mutual_friend',{/if}
            {/if}
            {if $is_picture_enabled}'picture',{/if}
            {if $is_video_enabled}'video',{/if}
            {if $is_forum_enabled}'forum',{/if}
            {if $is_note_enabled}'note',{/if}
            'visitor'
        ]);
    </script>

    <script>
        /* Google Map has issues with the screen map (it displays only gray screen) when it isn't visible when loaded (through profile ajax tabs), so just refresh the page to see correctly the map */
        $('ol#toc li a[href=#map]').click(function() {
            location.reload();
        });
    </script>

    {* Signup Popup *}
    {if !$is_logged AND !AdminCore::auth()}
        {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . $registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'signup_popup.js') }}
    {/if}
{else}
    <p class="center">{error}</p>
{/if}
