<?php
/**
 * @title          Generate a dynamic form from database fields
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class DynamicFieldCoreForm
{
    /** @var \PFBC\Form */
    private $oForm;

    /** @var string */
    private $sColumn;

    /** @var string */
    private $sVal;

    /**
     * @param \PFBC\Form $oForm
     * @param string $sValue Column name
     * @param string $sValue Field value
     */
    public function __construct(\PFBC\Form $oForm, $sColumn, $sValue)
    {
        $this->oForm = $oForm;
        $this->sColumn = $sColumn;
        $this->sVal = $sValue;
    }

    /**
     * Generate the dynamic form.
     *
     * @return \PFBC\Form
     */
    public function generate()
    {
        switch ($this->sColumn) {
            case 'description':
                $this->oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), $this->sColumn, ['id' => $this->getFieldId('str'), 'onblur' => 'CValid(this.value,this.id,10,2000)', 'value' => $this->sVal, 'validation' => new \PFBC\Validation\Str(20, 4000), 'required' => 1]));
                $this->addCheckErrSpan('str');
                break;

            case 'country':
                $this->oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), $this->sColumn, ['id' => $this->getFieldId('str'), 'value' => $this->sVal, 'required' => 1]));
                break;

            case 'city':
                $this->oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), $this->sColumn, ['id' => $this->getFieldId('str'), 'onblur' => 'CValid(this.value,this.id,2,150)', 'value' => $this->sVal, 'validation' => new \PFBC\Validation\Str(2, 150), 'required' => 1]));
                $this->addCheckErrSpan('str');
                break;

            case 'state':
                $this->oForm->addElement(new \PFBC\Element\Textbox(t('Your State/Province:'), $this->sColumn, ['id' => $this->getFieldId('str'), 'onblur' => 'CValid(this.value,this.id,2,150)', 'value' => $this->sVal, 'validation' => new \PFBC\Validation\Str(2, 150)]));
                $this->addCheckErrSpan('str');
                break;

            case 'zipCode':
                $this->oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), $this->sColumn, ['id' => $this->getFieldId('str'), 'onblur' => 'CValid(this.value,this.id,2,15)', 'value' => $this->sVal, 'validation' => new \PFBC\Validation\Str(2, 15)]));
                $this->addCheckErrSpan('str');
                break;

            case 'middleName':
                $this->oForm->addElement(new \PFBC\Element\Textbox(t('Middle Name:'), $this->sColumn, ['id' => $this->getFieldId('name'), 'onblur' => 'CValid(this.value,this.id)', 'value' => $this->sVal, 'validation' => new \PFBC\Validation\Name]));
                $this->addCheckErrSpan('name');
                break;

            case 'height':
                $this->oForm->addElement(new \PFBC\Element\Height(t('Height:'), $this->sColumn, ['value' => $this->sVal]));
                break;

            case 'weight':
                $this->oForm->addElement(new \PFBC\Element\Weight(t('Weight:'), $this->sColumn, ['value' => $this->sVal]));
                break;

            case 'website':
            case 'socialNetworkSite':
                $sLang = $this->sColumn === 'socialNetworkSite' ? t('Social Media Profile:') : t('Your Website:');
                $sDesc = $this->sColumn === 'socialNetworkSite' ? t('The URL of your social profile like Facebook, Snapchat, Instagram, Google+, etc.') : t('Your Personal Website/Blog (any promotional/affiliated contents will be removed)');
                $this->oForm->addElement(new \PFBC\Element\Url($sLang, $this->sColumn, ['id' => $this->getFieldId('url'), 'onblur' => 'CValid(this.value,this.id)', 'description' => $sDesc, 'value' => $this->sVal]));
                $this->addCheckErrSpan('url');
                break;

            case 'phone':
            case 'fax':
                $sLang = $this->sColumn === 'fax' ? t('Your Fax Number:') : t('Your Phone Number:');
                $this->oForm->addElement(new \PFBC\Element\Phone($sLang, $this->sColumn, ['id' => $this->getFieldId('phone'), 'onblur' => 'CValid(this.value, this.id)', 'title' => t('Enter full number with area code.'), 'value' => $this->sVal]));
                $this->addCheckErrSpan('phone');
                break;

            default: {
                $sLangKey = strtolower($this->sColumn);
                $sClass = '\PFBC\Element\\' . $this->getFieldType();
                $this->oForm->addElement(new $sClass(t($sLangKey), $this->sColumn, ['value' => $this->sVal]));
            }
        }

        return $this->oForm;
    }

    /**
     * @param string $sType
     *
     * @return string
     */
    protected function getFieldId($sType)
    {
        return $sType . '_' . $this->sColumn;
    }

    /**
     * @param string $sType
     *
     * @return void
     */
    protected function addCheckErrSpan($sType)
    {
        $this->oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ' . $this->getFieldId($sType) . '"></span>'));
    }

    /**
     * Generate other PFBC fields according to the Field Type.
     *
     * @return string PFBC form type.
     */
    protected function getFieldType()
    {
        if (strstr($this->sColumn, 'textarea'))
            $sType = 'Textarea';
        elseif (strstr($this->sColumn, 'editor'))
            $sType = 'CKEditor';
        elseif (strstr($this->sColumn, 'email'))
            $sType = 'Email';
        elseif (strstr($this->sColumn, 'password'))
            $sType = 'Password';
        elseif (strstr($this->sColumn, 'url'))
            $sType = 'Url';
        elseif (strstr($this->sColumn, 'phone'))
            $sType = 'Phone';
        elseif (strstr($this->sColumn, 'date'))
            $sType = 'Date';
        elseif (strstr($this->sColumn, 'color'))
            $sType = 'Color';
        elseif (strstr($this->sColumn, 'number'))
            $sType = 'Number';
        elseif (strstr($this->sColumn, 'range'))
            $sType = 'Range';
        elseif (strstr($this->sColumn, 'height'))
            $sType = 'Height';
        elseif (strstr($this->sColumn, 'weight'))
            $sType = 'Weight';
        else
            $sType = 'Textbox';

        return $sType;
    }
}
