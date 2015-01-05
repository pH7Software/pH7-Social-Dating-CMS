<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Global / View / Base / Error
 */
namespace PH7;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Layout\Html\Design;

$oDesign = new Design;
$oDesign->htmlHeader();
$aMeta = [
    'title' => 'Internal Server Error - ' . Core::SOFTWARE_NAME . ' | ' . Core::SOFTWARE_COMPANY,
    'description' => Core::SOFTWARE_DESCRIPTION,
    'keywords' => 'script, CMS, clone match, clone facebook, PHP, script dating'
];
?>
<!-- Begin Header -->
<?php $oDesign->usefulHtmlHeader($aMeta, true); ?>
<!-- End Header -->

<!-- Begin Content -->
<div id="content" class="s_padd">
<br />
<h1>Internal Server Error</h1>
<p>The server encountered an error. This is most often caused by a scripting problem, a failed database access attempt, or other similar reasons.</p>
</div>
<!-- End Content -->

<!-- Begin Footer -->
<footer>
<?php $oDesign->link(); ?>
</footer>
<!-- End Footer -->
<?php $oDesign->htmlFooter(); ?>
