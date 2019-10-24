<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 */

namespace PH7;

class Skeerel
{
    use Api; // Import the Api trait

    const JS_LIBRARY_URL = 'https://api.skeerel.com/assets/v2/javascript/api.min.js';
}
