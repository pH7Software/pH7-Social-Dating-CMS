<div class="signup_progress">
    <p class="signup_progress_label">
        <span>{lang 'Step %0% of %1%', $progressbar_step, $progressbar_total_steps}</span>
        <strong>{progressbar_percentage}%</strong>
    </p>
    <div
        class="progress"
        role="progressbar"
        aria-valuenow="{progressbar_percentage}"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuetext="{lang 'Step %0% of %1%', $progressbar_step, $progressbar_total_steps}"
    >
        <div class="progress-bar progress-bar-striped active" style="width:{progressbar_percentage}%">
            <span class="sr-only">{progressbar_percentage}%</span>
        </div>
    </div>
</div>
