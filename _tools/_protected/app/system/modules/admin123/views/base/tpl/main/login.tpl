<div class="col-md-8">
    {{ LoginForm::display() }}
    <p>{{ LostPwdDesignCore::link(PH7_ADMIN_MOD) }}</p>
    <p class="red">{lang 'Your logged IP is:'} <em class="bold">{ip}</em></p>
</div>