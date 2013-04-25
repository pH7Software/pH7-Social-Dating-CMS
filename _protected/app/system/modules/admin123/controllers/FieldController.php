<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */
namespace PH7;

class FieldController extends Controller
{

    private $sTitle;

    public function index()
    {
        Framework\Url\HeaderUrl::redirect(Framework\Mvc\Router\UriRoute::get(PH7_ADMIN_MOD, 'field', 'all'));
    }

    public function all()
    {
        $this->output();
    }

    public function edit()
    {
        $this->output();
    }

    public function add()
    {
        $this->output();
    }

    public function delete($iId)
    {
        $this->output();
    }

}
