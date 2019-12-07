{include file="inc/header.tpl"}

<h2>{$LANG.niche}</h2>

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}niche">
    <div class="col-md-4">
        <h3 class="underline">{$LANG.social_dating_niche}</h3>
        <p class="img_thumb">
            <img
                src="{$smarty.const.PH7_URL_ROOT}templates/themes/base/img/preview.gif"
                alt="Default Base Template"
                title="Default Base Template"
            />
        </p>
        <p class="bold italic underline dark-red">{$LANG.recommended}</p>
        <p class="bold">{$LANG.base_niche_desc}</p>
        <p class="italic">{$LANG.recommended_desc}</p>
        <p>
            <button type="submit" name="niche_submit" value="base" class="btn btn-primary btn-lg">{$LANG.go_social_dating}</button>
        </p>
    </div>

    <div class="col-md-4">
        <h3 class="underline">{$LANG.social_niche}</h3>
        <p class="img_thumb">
            <img
                src="{$smarty.const.PH7_URL_ROOT}templates/themes/zendate/img/preview.gif"
                alt="ZenDate Template"
                title="ZenDate Template"
            />
        </p>
        <p class="bold">{$LANG.zendate_niche_desc}</p>
        <p>
            <button type="submit" name="niche_submit" value="zendate" class="btn btn-primary btn-lg">{$LANG.go_social}</button>
        </p>
    </div>

    <div class="col-md-4">
        <h3 class="underline">{$LANG.dating_niche}</h3>
        <p class="img_thumb">
            <img
                src="{$smarty.const.PH7_URL_ROOT}templates/themes/datelove/img/preview.gif"
                alt="DateLove Template"
                title="DateLove Template"
            />
        </p>
        <p class="bold">
            {$LANG.datelove_niche_desc}
        </p>
        <p>
            <button type="submit" name="niche_submit" value="datelove" class="btn btn-primary btn-lg">{$LANG.go_dating}</button>
        </p>
    </div>

    <div class="s_tPadd clear"></div>
    <p class="bold italic">{$LANG.note_able_to_change_niche_settings_later}</p>
</form>

{include file="inc/footer.tpl"}
