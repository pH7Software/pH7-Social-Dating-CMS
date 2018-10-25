<?php
/**
 * @title            Page's DNA Generator
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;

defined('PH7') or exit('Restricted access');

final class PageDna
{
    const COMMENT_PH7CMS = <<<COMMENT
        \n<!--

            m    m mmmmmm   mmm  m    m  mmmm
     mmmm   #    #     #" m"   " ##  ## #"   "
     #" "#  #mmmm#    m"  #      # ## # "#mmm
     #   #  #    #   m"   #      # "" #     "#
     ##m#"  #    #  m"     "mmm" #    # "mmm#"
     #
     "

     Everything you Need to Create & Launch Your Own Social/Dating WebApp => http://pH7CMS.com
     https://github.com/pH7Software/pH7-Social-Dating-CMS

-->\n
COMMENT;

    const COMMENT_FOR_YOU = <<<COMMENT
        \n<!--

    mmmmmmm #        "
       #    # mm   mmm     mmm
       #    #"  #    #    #   "
       #    #   #    #     """m
       #    #   #  mm#mm  "mmm"

    m     m        #               "      m
    #  #  #  mmm   #mmm    mmm   mmm    mm#mm   mmm
    " #"# # #"  #  #" "#  #   "    #      #    #"  #
     ## ##" #""""  #   #   """m    #      #    #""""
     #   #  "#mm"  ##m#"  "mmm"  mm#mm    "mm  "#mm"

     mmmmmm                     m     m                 m
     #       mmm    m mm         "m m"   mmm   m   m    #
     #mmmmm #" "#   #"  "         "#"   #" "#  #   #    #
     #      #   #   #              #    #   #  #   #    "
     #      "#m#"   #              #    "#m#"  "mm"#    #

     Here you go! => http://pH7CMS.com
     https://github.com/pH7Software/pH7-Social-Dating-CMS

-->\n
COMMENT;

    const COMMENT_SOCIAL_DATING_SOFTWARE = <<<COMMENT
        \n<!--

      mmmm                  "           ""#
     #"   "  mmm    mmm   mmm     mmm     #
     "#mmm  #" "#  #"  "    #    "   #    #
         "# #   #  #        #    m"""#    #
     "mmm#" "#m#"  "#mm"  mm#mm  "mm"#    "mm

     mmmm            m      "
     #   "m  mmm   mm#mm  mmm    m mm    mmmm
     #    # "   #    #      #    #"  #  #" "#
     #    # m"""#    #      #    #   #  #   #
     #mmm"  "mm"#    "mm  mm#mm  #   #  "#m"#
                                         m  #
                                          ""

      mmmm           m""    m
     #"   "  mmm   mm#mm  mm#mm m     m  mmm    m mm   mmm
     "#mmm  #" "#    #      #   "m m m" "   #   #"  " #"  #
         "# #   #    #      #    #m#m#  m"""#   #     #""""
     "mmm#" "#m#"    #      "mm   # #   "mm"#   #     "#mm"

     EVERYTHING Your BUSINESS Needs to Create & Launch a SOCIAL/DATING WebApp => http://pH7CMS.com
     https://github.com/pH7Software/pH7-Social-Dating-CMS

-->\n
COMMENT;

    const COMMENTS = [
        self::COMMENT_PH7CMS,
        self::COMMENT_FOR_YOU,
        self::COMMENT_SOCIAL_DATING_SOFTWARE
    ];

    /**
     * @return string
     */
    public static function generateHtmlComment()
    {
        return self::COMMENTS[mt_rand(0, count(self::COMMENTS) - 1)];
    }
}
