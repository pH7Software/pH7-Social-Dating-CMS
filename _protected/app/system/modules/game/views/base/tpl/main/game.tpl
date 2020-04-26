<div class="center">
    {if empty($error)}
        <h3>{% $game->name %}</h3>

        {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'flash.js') }}
        <script>pH7DisplayFlash("{url_data_sys_mod}game/file/{% $game->file %}", '100%', 550);</script>

        <p>{% $game->description %}</p>

        <p>
            <a class="btn btn-default btn-sm" href="{{ $design->url('game','main','download',$game->gameId) }}">{lang 'Download this game'}</a>
        </p>

        <p class="italic">
            {lang '%0% was played %1% and download %2% time(s).', '<strong>'.$game->title.'</strong>', '<strong>'.$views.'</strong>', '<strong>'.$downloads.'</strong>'}
        </p>

        {{ RatingDesignCore::voting($game->gameId,DbTableName::GAME,'center') }}
        {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('game','main','game',"$game->title,$game->gameId")) }}
        {{ ShareEmbedCoreForm::display(PH7_URL_DATA_SYS_MOD . 'game/file/' . $game->file) }}

        {{ $design->socialMediaWidgets() }}

        {if AdminCore::auth()}
            <div>
                <a class="btn btn-default btn-sm" href="{{ $design->url('game','admin','edit',"$game->title,$game->gameId") }}">{lang 'Edit this Game'}</a> |
                <div class="btn btn-default btn-sm inline">
                    {{ LinkCoreForm::display(t('Delete this Game'), 'game', 'admin', 'delete', ['id'=>$game->gameId, 'thumb'=>$game->thumb, 'file'=>$game->file]) }}
                </div>
            </div>
        {/if}

        {{ CommentDesignCore::link($game->gameId, 'game') }}
    {else}
        <p>{error}</p>
    {/if}
</div>
