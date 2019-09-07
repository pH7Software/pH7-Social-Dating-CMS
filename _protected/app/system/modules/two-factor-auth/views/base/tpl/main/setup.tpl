<div class="center">
    {if $is_enabled}
        <p>{lang 'To use this, you have first to download a TOTP "Two-Factor Authentication" app such as <a href="%0%">Android</a> and <a href="%1%">iOS</a>.', $authenticator_android_app_url, $authenticator_ios_app_url}</p>
        <p><img src="{qr_core}" alt="Two-Factor authentication QR code" /></p>
        {{ LinkCoreForm::display('Download the backup recovery code', 'two-factor-auth', 'main', 'setup/' . $mod, array('get_backup_code' => 1)) }}
    {/if}

    {{ $text = !$is_enabled ? t('Turn On Two-Factor Authentication') : t('Turn Off Two-Factor Authentication') }}
    <div class="bold">
        {{ LinkCoreForm::display(
            $text,
            'two-factor-auth',
            'main',
            'setup/' . $mod,
            ['status' => $is_enabled]
        ) }}
    </div>
</div>
