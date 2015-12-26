<?php
/**
 * @title          Stat Ajax Class
 * @desc           Class of statistical data for the CMS in Ajax.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        0.6
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

class StatCoreAjax
{

    private $_oUserModel, $_mOutput;

    public function __construct()
    {
        $this->_oUserModel = new UserCoreModel;
        $this->_init();
    }

    public function display()
    {
        return $this->_mOutput;
    }

    private function _init()
    {
        switch( (new Http)->post('type') )
        {
            case 'total_users':
              $this->_mOutput = $this->_oUserModel->total();
            break;

           // If we receive another invalid value, we display a message with a HTTP header.
           default:
             Framework\Http\Http::setHeadersByCode(400);
           exit('Bad Request Error!');
       }
   }

}

echo (new StatCoreAjax)->display();
