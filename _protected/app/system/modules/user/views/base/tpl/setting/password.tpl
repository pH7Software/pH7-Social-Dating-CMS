<div class="col-md-8">
    {{ ChangePasswordCoreForm::display() }}
    {{ TwoFactorAuthDesignCore::link('user') }}
</div>

<div role="banner" class="right col-md-2 col-md-offset-2 ad_160_600">
    {{ $designModel->ad(160, 600) }}
</div>
