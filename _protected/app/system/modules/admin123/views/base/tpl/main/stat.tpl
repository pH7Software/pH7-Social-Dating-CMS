<script src="https://www.google.com/jsapi"></script>
<script>
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(showUserChart);

    function showUserChart() {
        $('#user_chart').html('');

        var oDate = new Date;
        var oDateOptions = {
           day: "numeric", month: "short", year: "numeric"
        };

        oDate.setFullYear(oDate.getFullYear());
        var sYear = oDate.toLocaleDateString('{% $config->values['language']['lang'] %}', oDateOptions);

        oDate.setMonth(oDate.getMonth()-1);
        var sMonth = oDate.toLocaleDateString('{% $config->values['language']['lang'] %}', oDateOptions);

        oDate.setDate(oDate.getDay()-7);
        var sWeek = oDate.toLocaleDateString('{% $config->values['language']['lang'] %}', oDateOptions);

        oDate.setDate(oDate.getDay());
        var sDay = oDate.toLocaleDateString('{% $config->values['language']['lang'] %}', oDateOptions);

        var aData = google.visualization.arrayToDataTable([
          ['{lang 'Time'}', '{lang 'All'}', '{lang 'Man'}', '{lang 'Women'}', '{lang 'Couples'}'],
          [sDay, {today_total_members}, {today_total_male_members}, {today_total_female_members}, {today_total_couple_members}],
          [sWeek, {week_total_members}, {week_total_male_members}, {week_total_female_members}, {week_total_couple_members}],
          [sMonth, {month_total_members}, {month_total_male_members}, {month_total_female_members}, {month_total_couple_members}],
          [sYear, {year_total_members}, {year_total_male_members}, {year_total_female_members}, {year_total_couple_members}]
        ]);

        var aOptions = {
          title: '{lang 'User Statistics'}'
        };

        new google.visualization.LineChart($('#user_chart')[0]).draw(aData, aOptions);
    }
</script>

<div id="user_chart"></div>

<div class="table-responsive panel panel-default">
    <div class="panel-heading bold">{lang '%site_name% Statistics'}</div>

    <table class="table table-striped">
        <tr>
            <th class="bold">{lang 'Quick Stats'}</th>
            <th class="bold">{lang 'Today'}</th>
            <th class="bold">{lang 'Last week'}</th>
            <th class="bold">{lang 'This month'}</th>
            <th class="bold">{lang 'This year (%0%)', date('Y')}</th>
            <th class="bold">{lang 'All (since %0%)', $since_date}</th>
        </tr>

        <tr>
            <td>{lang 'All Member Logins'}</td>
            <td>{today_login_members}</td>
            <td>{week_login_members}</td>
            <td>{month_login_members}</td>
            <td>{year_login_members}</td>
            <td>{login_members}</td>
        </tr>

        <tr>
            <td>{lang 'Men Member Logins'}</td>
            <td>{today_login_male_members}</td>
            <td>{week_login_male_members}</td>
            <td>{month_login_male_members}</td>
            <td>{year_login_male_members}</td>
            <td>{login_male_members}</td>
        </tr>

        <tr>
            <td>{lang 'Women Member Logins'}</td>
            <td>{today_login_female_members}</td>
            <td>{week_login_female_members}</td>
            <td>{month_login_female_members}</td>
            <td>{year_login_female_members}</td>
            <td>{login_female_members}</td>
        </tr>

        <tr>
            <td>{lang 'Couple Member Logins'}</td>
            <td>{today_login_couple_members}</td>
            <td>{week_login_couple_members}</td>
            <td>{month_login_couple_members}</td>
            <td>{year_login_couple_members}</td>
            <td>{login_couple_members}</td>
        </tr>

        <tr>
            <td>{lang 'All Affiliate Logins'}</td>
            <td>{today_login_affiliate}</td>
            <td>{week_login_affiliate}</td>
            <td>{month_login_affiliate}</td>
            <td>{year_login_affiliate}</td>
            <td>{login_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'Men Affiliate Logins'}</td>
            <td>{today_login_male_affiliate}</td>
            <td>{week_login_male_affiliate}</td>
            <td>{month_login_male_affiliate}</td>
            <td>{year_login_male_affiliate}</td>
            <td>{login_male_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'Women Affiliate Logins'}</td>
            <td>{today_login_female_affiliate}</td>
            <td>{week_login_female_affiliate}</td>
            <td>{month_login_female_affiliate}</td>
            <td>{year_login_female_affiliate}</td>
            <td>{login_female_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'All Admin Logins'}</td>
            <td>{today_login_admins}</td>
            <td>{week_login_admins}</td>
            <td>{month_login_admins}</td>
            <td>{year_login_admins}</td>
            <td>{login_admins}</td>
        </tr>

        <tr>
            <td>{lang 'Men Admin Logins'}</td>
            <td>{today_login_male_admins}</td>
            <td>{week_login_male_admins}</td>
            <td>{month_login_male_admins}</td>
            <td>{year_login_male_admins}</td>
            <td>{login_male_admins}</td>
        </tr>

        <tr>
            <td>{lang 'Women Admin Logins'}</td>
            <td>{today_login_female_admins}</td>
            <td>{week_login_female_admins}</td>
            <td>{month_login_female_admins}</td>
            <td>{year_login_female_admins}</td>
            <td>{login_female_admins}</td>
        </tr>

        <tr>
            <td>{lang 'All Member Registrations'}</td>
            <td>{today_total_members}</td>
            <td>{week_total_members}</td>
            <td>{month_total_members}</td>
            <td>{year_total_members}</td>
            <td>{total_members}</td>
        </tr>

        <tr>
            <td>{lang 'Men Member Registrations'}</td>
            <td>{today_total_male_members}</td>
            <td>{week_total_male_members}</td>
            <td>{month_total_male_members}</td>
            <td>{year_total_male_members}</td>
            <td>{total_male_members}</td>
        </tr>

        <tr>
            <td>{lang 'Women Member Registrations'}</td>
            <td>{today_total_female_members}</td>
            <td>{week_total_female_members}</td>
            <td>{month_total_female_members}</td>
            <td>{year_total_female_members}</td>
            <td>{total_female_members}</td>
        </tr>

        <tr>
            <td>{lang 'Couple Member Registrations'}</td>
            <td>{today_total_couple_members}</td>
            <td>{week_total_couple_members}</td>
            <td>{month_total_couple_members}</td>
            <td>{year_total_couple_members}</td>
            <td>{total_couple_members}</td>
        </tr>

        <tr>
            <td>{lang 'All Affiliate Registrations'}</td>
            <td>{today_total_affiliate}</td>
            <td>{week_total_affiliate}</td>
            <td>{month_total_affiliate}</td>
            <td>{year_total_affiliate}</td>
            <td>{total_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'Men Affiliate Registrations'}</td>
            <td>{today_total_male_affiliate}</td>
            <td>{week_total_male_affiliate}</td>
            <td>{month_total_male_affiliate}</td>
            <td>{year_total_male_affiliate}</td>
            <td>{total_male_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'Women Affiliate Registrations'}</td>
            <td>{today_total_female_affiliate}</td>
            <td>{week_total_female_affiliate}</td>
            <td>{month_total_female_affiliate}</td>
            <td>{year_total_female_affiliate}</td>
            <td>{total_female_affiliate}</td>
        </tr>

        <tr>
            <td>{lang 'All Admin Registrations'}</td>
            <td>{today_total_admins}</td>
            <td>{week_total_admins}</td>
            <td>{month_total_admins}</td>
            <td>{year_total_admins}</td>
            <td>{total_admins}</td>
        </tr>

        <tr>
            <td>{lang 'Men Admin Registrations'}</td>
            <td>{today_total_male_admins}</td>
            <td>{week_total_male_admins}</td>
            <td>{month_total_male_admins}</td>
            <td>{year_total_male_admins}</td>
            <td>{total_male_admins}</td>
        </tr>

        <tr>
            <td>{lang 'Women Admin Registrations'}</td>
            <td>{today_total_female_admins}</td>
            <td>{week_total_female_admins}</td>
            <td>{month_total_female_admins}</td>
            <td>{year_total_female_admins}</td>
            <td>{total_female_admins}</td>
        </tr>

        <tr>
            <td>{lang 'Blogs'}</td>
            <td>{today_total_blogs}</td>
            <td>{week_total_blogs}</td>
            <td>{month_total_blogs}</td>
            <td>{year_total_blogs}</td>
            <td>{total_blogs}</td>
        </tr>

        <tr>
            <td>{lang 'Notes'}</td>
            <td>{today_total_notes}</td>
            <td>{week_total_notes}</td>
            <td>{month_total_notes}</td>
            <td>{year_total_notes}</td>
            <td>{total_notes}</td>
        </tr>

        <tr>
            <td>{lang 'Private Messages'}</td>
            <td>{today_total_mails}</td>
            <td>{week_total_mails}</td>
            <td>{month_total_mails}</td>
            <td>{year_total_mails}</td>
            <td>{total_mails}</td>
        </tr>

        <tr>
            <td>{lang 'Profile Comments'}</td>
            <td>{today_total_profile_comments}</td>
            <td>{week_total_profile_comments}</td>
            <td>{month_total_profile_comments}</td>
            <td>{year_total_profile_comments}</td>
            <td>{total_profile_comments}</td>
        </tr>

        <tr>
          <td>{lang 'Photo Comments'}</td>
          <td>{today_total_picture_comments}</td>
          <td>{week_total_picture_comments}</td>
          <td>{month_total_picture_comments}</td>
          <td>{year_total_picture_comments}</td>
          <td>{total_picture_comments}</td>
        </tr>

        <tr>
            <td>{lang 'Video Comments'}</td>
            <td>{today_total_video_comments}</td>
            <td>{week_total_video_comments}</td>
            <td>{month_total_video_comments}</td>
            <td>{year_total_video_comments}</td>
            <td>{total_video_comments}</td>
        </tr>

        <tr>
           <td>{lang 'Blog Comments'}</td>
            <td>{today_total_blog_comments}</td>
            <td>{week_total_blog_comments}</td>
            <td>{month_total_blog_comments}</td>
            <td>{year_total_blog_comments}</td>
            <td>{total_blog_comments}</td>
        </tr>

        <tr>
            <td>{lang 'Note Comments'}</td>
            <td>{today_total_note_comments}</td>
            <td>{week_total_note_comments}</td>
            <td>{month_total_note_comments}</td>
            <td>{year_total_note_comments}</td>
            <td>{total_note_comments}</td>
        </tr>

        <tr>
            <td>{lang 'Game Comments'}</td>
            <td>{today_total_game_comments}</td>
            <td>{week_total_game_comments}</td>
            <td>{month_total_game_comments}</td>
            <td>{year_total_game_comments}</td>
            <td>{total_game_comments}</td>
        </tr>
    </table>
</div>
