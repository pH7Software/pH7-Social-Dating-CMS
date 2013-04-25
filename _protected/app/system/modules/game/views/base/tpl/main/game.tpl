<div class="center">

{@if(empty($error))@}

  <h3>{% $game->name %}</h3>

  {{ $design->staticFiles('js', PH7_STATIC . PH7_JS, 'flash.js') }}
  <script>pH7DisplayFlash("{url_data_sys_mod}game/file/{% $game->file %}",730,550);</script>

  <p>{% $game->description %}</p>

  <p><a class="m_button" href="{{ $design->url('game','main','download',$game->gameId) }}">{@lang('Download this game')@}</a></p>

  <p class="italic">{@lang('%0% was played %1% and download %2%.','<strong>'.$game->title.'</strong>',Framework\Mvc\Model\Statistic::getView($game->gameId,'Games'),$downloads)@}</p>

  {{ RatingDesignCore::voting($game->gameId,'Games','center') }}

  {{ $design->likeApi() }}

  {@if(AdminCore::auth())@}
    <p><a class="m_button" href="{{ $design->url('game','admin','edit',"$game->title,$game->gameId") }}">{@lang('Edit this Game')@}</a> | <div class="m_button inline">{{ LinkCoreForm::display(t('Delete this Game'), 'game', 'admin', 'delete', array('id'=>$game->gameId, 'thumb'=>$game->thumb, 'file'=>$game->file)) }}</div></div>
  {@/if@}

  {{ CommentDesignCore::link($game->gameId, 'Game') }}

{@else@}

<p>{error}</p>

{@/if@}

</div>
