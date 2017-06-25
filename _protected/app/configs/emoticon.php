<?php
/**
 * @title          Emoticon File
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @link           http://ph7cms.com
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/**
 * The emoticons are in ~/static/img/smile/ folder.
 */

return array(

/** smiley (image name without extension)      |      code        |       alt    **/

    'grin'             =>    array(array(':-d', ':d', 'xd', '=d'), 'Grin'),
    'lol'              =>    array(array('^^', ':lol:'), 'LOL'),
    'cheese'           =>    array(':cheese:',    'Cheese'),
    'smile'            =>    array(array(':)', ':-)', ':]', '=)'), 'Smile'),
    'wink'             =>    array(array(';)', ';-)'), 'Wink'),
    'smirk'            =>    array(':smirk:', 'Smirk'),
    'roll'             =>    array(':rolling:', 'Rolleyes'),
    'confused'         =>    array(':-S', 'Confused'),
    'surprise'         =>    array(':wow:', 'Surprised'),
    'bigsurprise'      =>    array(':bug:', 'Big Surprise'),
    'tongue_laugh'     =>    array(':-P', 'Tongue Laugh'),
    'tongue_rolleye'   =>    array('%-P', 'Tongue Rolleye'),
    'tongue_wink'      =>    array(';-p', 'Tongue Wink'),
    'rasberry'         =>    array(array(':-p', ':p', '=p'), 'Rasberry'),
    'blank'            =>    array(':blank:', 'Blank Stare'),
    'longface'         =>    array(':long:', 'Long Face'),
    'ohh'              =>    array(':ohh:', 'Ohh'),
    'grrr'             =>    array(':grrr:', 'Grrr'),
    'gulp'             =>    array(':gulp:', 'Gulp'),
    'ohoh'             =>    array('8-/', 'Oh oh'),
    'downer'           =>    array(':down:',    'Downer'),
    'embarrassed'      =>    array(':red:', 'Red face'),
    'sick'             =>    array(':sick:', 'Sick'),
    'shuteye'          =>    array(':shut:', 'Shut eye'),
    'hmm'              =>    array(':-/', 'Hmmm'),
    'mad'              =>    array('>:(', 'Mad'),
    'angry'            =>    array('>:-(', 'Angry'),
    'zip'              =>    array(':zip:', 'Zipper'),
    'kiss'             =>    array(array(':-* :*', ':kiss:'), 'Kiss'),
    'shock'            =>    array(':ahhh:', 'Shock'),
    'shade_smile'      =>    array(':coolsmile:', 'Cool smile'),
    'shade_smirk'      =>    array(':coolsmirk:', 'Cool smirk'),
    'shade_grin'       =>    array(':coolgrin:', 'Cool grin'),
    'shade_hmm'        =>    array(':coolhmm:', 'Cool hmm'),
    'shade_mad'        =>    array(':coolmad:', 'Cool mad'),
    'shade_cheese'     =>    array(':coolcheese:', 'Cool cheese'),
    'vampire'          =>    array(':vampire:', 'Vampire'),
    'snake'            =>    array(array('°°', ':snake:'), 'Snake'),
    'exclaim'          =>    array(':exclaim:', 'Excaim'),
    'vampire'          =>    array(':vampire:', 'Vampire'),
    'question'         =>    array(array(':ask:', ':question:'), 'Question')

);
