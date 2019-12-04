<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
    'noindex' => true
];
?>
<!-- Begin Header -->
<?php $oDesign->usefulHtmlHeader($aMeta, true); ?>
<!-- End Header -->

<!-- Begin Content -->
<div id="content" class="center s_padd">
    <br />
    <h1>Internal Server Error</h1>
    <p>The server encountered an error. This is most often caused by a scripting problem, a failed database access
        attempt, or other similar reasons.
    </p>

    <p>If you are the administrator of the site, please go to your admin panel -> Tools -> Environment Mode and set to
        "Development", then go back here to see the details of the error.
    </p>
</div>
<!-- End Content -->

<!-- Begin Footer -->
<footer>
    <p><?php $oDesign->link(); ?></p>
</footer>
<!-- End Footer -->
<?php $oDesign->htmlFooter(); ?>
