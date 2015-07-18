<?php
/**
 * @title            User API Class
 * @desc             This class allows to communicate with pH7CMS's user data from an external application (e.g., iOS, Android, website, ...).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Api
 * @version          1.0
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class UserCoreApi extends Framework\Http\Rest\Rest
{

    public function __construct()
    {
        parent::__construct();
    }
	
	private function createAccount()
	{
		
	}
	
	private function login()
	{
		
	}
	
	public function getUser($iId)
	{
		
	}
	
	public function getUsers()
	{
		
	}

}

// Run the User API!
new UserCoreApi;
