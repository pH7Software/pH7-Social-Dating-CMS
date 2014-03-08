<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Module / Virtual Earth / Controller
 */
namespace PH7;

class MainController extends Controller
{

    public function index()
    {
        $this->view->page_title = t('Virtual Earth 3D !');
        $this->view->meta_description = t('Virtual World, Earth 3D. A virtual Life 3D revolutionary ! Best Virtual Free Online Dating 3D !');
        $this->view->meta_keywords = t('virtual, world, earth, life, 3D, dating 3d, online, second life, free online dating, free');
        $this->view->h1_title = t('Virtual Earth 3D, Revolutionary Online Virtual World 3D !');
        $this->output();
    }

}
