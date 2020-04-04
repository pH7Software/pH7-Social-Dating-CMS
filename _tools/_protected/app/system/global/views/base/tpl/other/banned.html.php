<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Global / View / Base / Other
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;

$oDesign = new Design;
$oDesign->htmlHeader();

$aMeta = [
    'title' => t('Free Dating Social Community - Your IP is banned') . ' - ' . Core::SOFTWARE_NAME . ' | ' . Core::SOFTWARE_COMPANY,
    'description' => t('Free Dating Social Community - Your IP is banned') . ' ' . Core::SOFTWARE_DESCRIPTION,
    'noindex' => true
];
?>
<!-- Begin Header -->
<?php $oDesign->usefulHtmlHeader($aMeta, true); ?>
<!-- End Header -->

<!-- Begin Content -->
<div id="content" class="center s_padd">
    <br />
    <h1 class="err_msg"><?php echo t('Your IP or your location is banned!') ?></h1>
    <p>
        <?php echo t('Sorry, your IP address or your location is banned for this site.') ?>
        <br /><br />
        <span class="small italic"><?php echo t("Regards, %site_name%'s Team.") ?></span>
    </p>
</div>
<!-- End Content -->

<!-- Begin Footer -->
<footer>
    <p><?php $oDesign->link(); ?></p>
</footer>
<!-- End Footer -->
<?php $oDesign->htmlFooter(); ?>
