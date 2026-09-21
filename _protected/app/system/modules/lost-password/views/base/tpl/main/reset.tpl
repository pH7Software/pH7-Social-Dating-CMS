<div class="col-md-6">
    <p>{lang}Choose a new password for your account. You will then be asked to sign in.{/lang}</p>
    {{ ResetPasswordForm::display($reset_mod, $reset_email, $reset_token) }}
</div>
