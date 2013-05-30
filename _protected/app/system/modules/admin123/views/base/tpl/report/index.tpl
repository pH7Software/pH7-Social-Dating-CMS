<div class="center">

{@if(empty($reports))@}

<p class="bold">{@lang('Empty Report')@}</p>

{@else@}

<form method="post" action="{{ $design->url(PH7_ADMIN_MOD,'report','inbox') }}">
{{ $designSecurity->inputToken('report_action') }}

<ul>

{@foreach($reports as $report)@}
  <li id="report_{% $report->reportId %}"><input type="checkbox" name="action[]" value="{% $report->reportId %}" /> | <a href="{% $design->url(PH7_ADMIN_MOD, 'report', 'report', $report->reportId) %}">{@lang('View Report')@}</a> | {@lang('Reporter:')@} {{ $avatarDesign->get($oUserModel->getUsername($report->reporterId), $oUserModel->getFirstName($report->reporterId) ,null, 32) }} | {@lang('Spammer:')@} {{ $avatarDesign->get($oUserModel->getUsername($report->spammerId), $oUserModel->getFirstName($report->spammerId) ,null, 32) }} <a class="m_button" href="javascript:void(0)" onclick="report('delete', {% $report->reportId%},'{csrf_token}')">{@lang('Delete')@}</a></li>
{@/foreach@}

</ul>

<p><input type="checkbox" name="all_action" /> <button type="submit" onclick="return checkChecked(false)" formaction="{{ $design->url(PH7_ADMIN_MOD,'report','deleteall') }}">{@lang('Delete')@}</button></p>

</form>

{@main_include('page_nav.inc.tpl')@}

{@/if@}

</div>
