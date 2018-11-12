<div class="box-left col-md-3 col-lg-3 col-xl-2">
    <div class="design-box">
        <h2>{lang 'Search Games'}</h2>
        {{ SearchGameForm::display(PH7_WIDTH_SEARCH_FORM) }}
    </div>

    <div class="design-box">
        <h2>{lang 'Categories'}</h2>
        <ul>
            {each $category in $categories}
                <li>
                    <a href="{{ $design->url('game','main','category',$category->name) }}" title="{% $category->name %}" data-load="ajax">{% $category->name %}</a> - ({% $category->totalCatGames %})
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Popular Games'}</h2>
        <ul>
            {each $views in $top_views}
                <li>
                    <a href="{{ $design->url('game','main','game',"$views->title,$views->gameId") }}" title="{% $views->name %}">{% $views->title %}</a>
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Rated Games'}</h2>
        <ul>
            {each $rating in $top_rating}
                <li>
                  <a href="{{ $design->url('game','main','game',"$rating->title,$rating->gameId") }}" title="{% $rating->name %}">{% $rating->title %}</a>
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Newest Games'}</h2>
        <ul>
            {each $new in $latest}
                <li>
                  <a href="{{ $design->url('game','main','game',"$new->title,$new->gameId") }}" title="{% $new->name %}">{% $new->title %}</a>
                </li>
            {/each}
        </ul>
    </div>
</div>

<div class="box-right col-md-9 col-lg-9 col-xl-9 col-xl-offset-1">
    <div class="center">
        {if !empty($error)}
            <p>{error}</p>
        {else}
            {each $game in $games}
                <h2>
                    <a href="{{ $design->url('game','main','game',"$game->title,$game->gameId") }}">{% $game->title %}</a>
                </h2>

                <div class="msg_content">
                    <p>
                        <a rel="nofollow" href="{{ $design->url('game','main','game',"$game->title,$game->gameId") }}">
                            <img alt="{% $game->title %}" title="{% $game->title %}" src="{url_data_sys_mod}game/img/thumb/{% $game->thumb %}" width="95" height="66" class="thumb_img" />
                        </a>
                    </p>
                    <p><strong>{% $game->title %}</strong></p>
                    <p>{% $game->description %}</p>

                    {if AdminCore::auth()}
                        <div>
                            <a class="btn btn-default btn-sm" href="{{ $design->url('game','admin','edit',"$game->title,$game->gameId") }}">{lang 'Edit Game'}</a> &bull;
                            <div class="btn btn-default btn-sm inline">
                                {{ LinkCoreForm::display(t('Delete Game'), 'game', 'admin', 'delete', ['id'=>$game->gameId, 'thumb'=>$game->thumb, 'file'=>$game->file]) }}
                            </div>
                        </div>
                    {/if}
                    <hr />
                </div>
            {/each}

            {main_include 'page_nav.inc.tpl'}
        {/if}
    </div>
</div>
