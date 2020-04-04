<div class="col-md-8">
    {{ EditForm::display() }}

    {if !$is_edit_mode}
        {{ TwoFactorAuthDesignCore::link(PH7_ADMIN_MOD) }}
    {/if}
</div>
