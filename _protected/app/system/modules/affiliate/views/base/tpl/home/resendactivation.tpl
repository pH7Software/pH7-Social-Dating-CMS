<div class="left col-md-8">
    <p>
        {lang}Simply enter your registered email address in the field below, and click the "Send Activation" button.{/lang}<br />
        {lang}It must be the same email address you gave us when you became a member.{/lang}
    </p>

    {{ ResendActivationCoreForm::display(DbTableName::AFFILIATE) }}

    <p>
        {lang}After you click the "Send Activation" button, the account activation email will be sent to you, using this email address.{/lang}<br />
        {lang}You should receive the email after a few minutes.{/lang}
    </p>
</div>

<div class="right col-md-4 ad_336_280">
    {designModel.ad(336, 280)}
</div>
