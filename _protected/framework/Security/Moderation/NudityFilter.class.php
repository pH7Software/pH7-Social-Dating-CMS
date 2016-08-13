<?php
/**
 * @title            Nudity Filter class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Moderation
 */

namespace PH7\Framework\Security\Moderation;
defined('PH7') or exit('Restricted access');

class NudityFilter
{
  public static function isNudity($sPath)
  {
      self::importLibrary();
      return \Image_FleshSkinQuantifier($sPath)->isPorn())
  }

  protected static function importLibrary()
  {
      Import::lib('FreebieStock.NudityDetector.Autoloader');
  }

  /**
   * Private constructor to prevent instantiation since it is a static class.
   */
  private function __construct() {}
}
