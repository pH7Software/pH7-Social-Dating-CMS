<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Global / View / Base / Other
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;

$oDesign = new Design;
$oDesign->htmlHeader();

$aMeta = [
    'title' => Core::SOFTWARE_NAME . ' | ' . Core::SOFTWARE_COMPANY,
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
    <h1><?php echo t('Whoops!') ?></h1>
    <p>&nbsp;</p>
    <p class="center"><?php echo $sMsg ?></p>
</div>
<!-- End Content -->

<!-- Begin Footer -->
<footer>
    <p><?php $oDesign->link(); ?></p>
</footer>
<!-- End Footer -->
<?php $oDesign->htmlFooter(); ?>
