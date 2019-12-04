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

echo '<html><head><meta charset="utf-8" /><title>Fatal Error: Exception</title><meta name="author" content="pH7CMS, Pierre-Henry Soria" /><meta name="copyright" content="(c) 2012-2019, Pierre-Henry Soria. All Rights Reserved" /><meta name="creator" content="pH7CMS, Pierre-Henry Soria" /><meta name="designer" content="pH7CMS, Pierre-Henry Soria" /><meta name="generator" content="pH7CMS" /><style>.debug_cont{font-family:Arial,Verdana,Helvetica,sans-serif;font-size:14px;color:#333;padding:15px 0;width:100%;margin:0 auto}.debug_body{background:#fff;border:4px double;padding:5px}.debug_cap{font:bold 13px "Trebuchet MS",Verdana,Helvetica,Arial,serif;color:#fff;padding:5px;border:1px solid #000;width:250px;margin-top:-20px;margin-bottom:10px}.debug_body .notice{background:#fdf403;color:#555}.debug_body .warning{background:#f8b423;color:#555}.debug_body .error{background:#c10505;color:#fff}.debug_body .exception{background:#093dd3;color:#fff}.debug_body .vardump{background:#333;color:#fff}.vardumper .string{color:green}.vardumper .null,.vardumper .array,.vardumper .bool{color:blue}.vardumper .property{color:brown}.vardumper .number{color:red}.vardumper .class{color:black}.vardumper .class_prop{color:brown}pre{font-size:12px;border:1px solid #36c;background-color:#e5ecf9;padding:.5em}</style></head><body><div class="debug_cont"><div class="debug_body"><div class="debug_cap exception">'.Core::SOFTWARE_NAME.' Debug - Exception</div><table><tr><td>Message:</td><td>'.$oExcept->getMessage().'</td></tr><tr><td>File:</td><td>'.$oExcept->getFile().'</td></tr><tr><td>Line:</td><td>'.$oExcept->getLine().'</td></tr><tr><td>Trace:</td><td><pre>'.$oExcept->getTraceAsString().'</pre></td></tr></table></div></div>';

$oDesign->htmlFooter();
