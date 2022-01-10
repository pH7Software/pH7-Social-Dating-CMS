<div class="col-md-10">
  <h3 class="underline">
      {lang 'Your Unique Referral Link'}
  </h3>

  {{ ShareUrlCoreForm::display($referral_link_url, null, false) }}

  <p>
    <a href="{tweet_msg_url}" target="_blank" rel="noopener noreferrer">
        {lang 'Share on Twitter'}
    </a> 🐦
  </p>
</div>
