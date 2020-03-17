<?php
/**
 * @title          Emoticon File
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @link           https://ph7cms.com
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/**
 * The emoticons are in ~/static/img/smile/ folder.
 */

return [
    /** smiley (image name without file extension)  |  code  |  alt **/

    'grin'             =>    [[':-d', ':d', 'xd', '=d'], 'Grin'],
    'lol'              =>    [['^^', ':lol:'], 'LOL'],
    'cheese'           =>    [':cheese:',    'Cheese'],
    'smile'            =>    [[':)', ':-)', ':]', '=)'], 'Smile'],
    'wink'             =>    [[';)', ';-)'], 'Wink'],
    'smirk'            =>    [':smirk:', 'Smirk'],
    'roll'             =>    [':rolling:', 'Rolleyes'],
    'confused'         =>    [':-S', 'Confused'],
    'surprise'         =>    [':wow:', 'Surprised'],
    'bigsurprise'      =>    [':bug:', 'Big Surprise'],
    'tongue_laugh'     =>    [':-P', 'Tongue Laugh'],
    'tongue_rolleye'   =>    ['%-P', 'Tongue Rolleye'],
    'tongue_wink'      =>    [';-p', 'Tongue Wink'],
    'rasberry'         =>    [[':-p', ':p', '=p'], 'Rasberry'],
    'blank'            =>    [':blank:', 'Blank Stare'],
    'longface'         =>    [':long:', 'Long Face'],
    'ohh'              =>    [':ohh:', 'Ohh'],
    'grrr'             =>    [':grrr:', 'Grrr'],
    'gulp'             =>    [':gulp:', 'Gulp'],
    'ohoh'             =>    ['8-/', 'Oh oh'],
    'downer'           =>    [':down:',    'Downer'],
    'embarrassed'      =>    [':red:', 'Red face'],
    'sick'             =>    [':sick:', 'Sick'],
    'shuteye'          =>    [':shut:', 'Shut eye'],
    'hmm'              =>    [':-/', 'Hmmm'],
    'mad'              =>    ['>:(', 'Mad'],
    'angry'            =>    ['>:-(', 'Angry'],
    'zip'              =>    [':zip:', 'Zipper'],
    'kiss'             =>    [[':-* :*', ':kiss:'], 'Kiss'],
    'shock'            =>    [':ahhh:', 'Shock'],
    'shade_smile'      =>    [':coolsmile:', 'Cool smile'],
    'shade_smirk'      =>    [':coolsmirk:', 'Cool smirk'],
    'shade_grin'       =>    [':coolgrin:', 'Cool grin'],
    'shade_hmm'        =>    [':coolhmm:', 'Cool hmm'],
    'shade_mad'        =>    [':coolmad:', 'Cool mad'],
    'shade_cheese'     =>    [':coolcheese:', 'Cool cheese'],
    'vampire'          =>    [':vampire:', 'Vampire'],
    'snake'            =>    [['°°', ':snake:'], 'Snake'],
    'exclaim'          =>    [':exclaim:', 'Excaim'],
    'vampire'          =>    [':vampire:', 'Vampire'],
    'question'         =>    [[':ask:', ':question:'], 'Question']
];
