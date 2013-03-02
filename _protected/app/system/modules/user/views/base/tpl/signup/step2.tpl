<div id="progress_bar"><label id="percent"></label></div>
{{ JoinForm::step2() }}

<script>$('#progress_bar').progressbar({value:66});$('#progress_bar').css('width','600px');$('#percent').text('66% - STEP 2/3');</script>
