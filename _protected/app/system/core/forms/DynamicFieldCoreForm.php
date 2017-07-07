<?php
/**
 * @title          Generate a dynamic form from database fields
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class DynamicFieldCoreForm
{
    private $_oForm, $_sColumn, $_sVal;

    /**
     * @param object \PFBC\Form $oForm
     * @param string $sValue Column name
     * @param string $sValue Field value
     */
    public function __construct(\PFBC\Form $oForm, $sColumn, $sValue)
    {
        $this->_oForm = $oForm;
        $this->_sColumn = $sColumn;
        $this->_sVal = $sValue;
    }

    /**
     * Generate the dynamic form.
     *
     * @return object \PFBC\Form
     */
    public function generate()
    {
        switch ($this->_sColumn)
        {
            case 'description':
                $this->_oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), $this->_sColumn, array('id' => $this->getFieldId('str'), 'onblur' => 'CValid(this.value,this.id,10,2000)','value' => $this->_sVal, 'validation' => new \PFBC\Validation\Str(20,4000), 'required' => 1)));
                $this->addCheckErrSpan('str');
            break;

            case 'country':
                $this->_oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), $this->_sColumn, array('id'=>$this->getFieldId('str'), 'value' => $this->_sVal, 'required'=>1)));
            break;

            case 'city':
                $this->_oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), $this->_sColumn, array('id'=>$this->getFieldId('str'), 'onblur' =>'CValid(this.value,this.id,2,150)','value' => $this->_sVal, 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
                $this->addCheckErrSpan('str');
            break;

            case 'state':
                $this->_oForm->addElement(new \PFBC\Element\Textbox(t('Your State/Province:'), $this->_sColumn, array('id'=>$this->getFieldId('str'), 'onblur' =>'CValid(this.value,this.id,2,150)','value' => $this->_sVal, 'validation'=>new \PFBC\Validation\Str(2,150))));
                $this->addCheckErrSpan('str');
            break;

            case 'zipCode':
                $this->_oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), $this->_sColumn, array('id'=>$this->getFieldId('str'), 'onblur' =>'CValid(this.value,this.id,2,15)','value' => $this->_sVal, 'validation'=>new \PFBC\Validation\Str(2,15))));
                $this->addCheckErrSpan('str');
            break;

            case 'middleName':
                $this->_oForm->addElement(new \PFBC\Element\Textbox(t('Middle Name:'), $this->_sColumn, array('id'=>$this->getFieldId('name'), 'onblur' =>'CValid(this.value,this.id)', 'value' => $this->_sVal, 'validation'=>new \PFBC\Validation\Name)));
                $this->addCheckErrSpan('name');
            break;

            case 'height':
                $this->_oForm->addElement(new \PFBC\Element\Height(t('Height:'), $this->_sColumn, array('value' => $this->_sVal)));
            break;

            case 'weight':
                $this->_oForm->addElement(new \PFBC\Element\Weight(t('Weight:'), $this->_sColumn, array('value' => $this->_sVal)));
            break;

            case 'website':
            case 'socialNetworkSite':
                $sLang = ($this->_sColumn == 'socialNetworkSite') ? t('Social Media Profile:') : t('Your Website:');
                $sDesc = ($this->_sColumn == 'socialNetworkSite') ? t('The URL of your social profile like Facebook, Snapchat, Instagram, Google+, etc.') : t('Your Personal Website/Blog (any promotional/affiliated contents will be banned)');
                $this->_oForm->addElement(new \PFBC\Element\Url($sLang, $this->_sColumn, array('id'=>$this->getFieldId('url'), 'onblur'=>'CValid(this.value,this.id)', 'description'=>$sDesc, 'value' => $this->_sVal)));
                $this->addCheckErrSpan('url');
            break;

            case 'phone':
            case 'fax':
                $sLang = ($this->_sColumn == 'fax') ? t('Your Fax Number:') : t('Your Phone Number:');
                $this->_oForm->addElement(new \PFBC\Element\Phone($sLang, $this->_sColumn, array('id'=>$this->getFieldId('phone'), 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('Enter full number with area code.'), 'value' => $this->_sVal)));
                $this->addCheckErrSpan('phone');
            break;

            default:
            {
                $sLangKey = strtolower($this->_sColumn);
                $sClass = '\PFBC\Element\\' . $this->getFieldType();
                $this->_oForm->addElement(new $sClass(t($sLangKey), $this->_sColumn, array('value' => $this->_sVal)));
            }
        }

        return $this->_oForm;
    }

    protected function getFieldId($sType)
    {
        return $sType . '_' . $this->_sColumn;
    }

    protected function addCheckErrSpan($sType)
    {
        $this->_oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ' . $this->getFieldId($sType) . '"></span>'));
    }

    /**
     * Generate other PFBC fields according to the Field Type.
     *
     * @return string PFBC Form Type
     */
    protected function getFieldType()
    {
        if (strstr($this->_sColumn, 'textarea'))
            $sType = 'Textarea';
        elseif (strstr($this->_sColumn, 'editor'))
            $sType = 'CKEditor';
        elseif (strstr($this->_sColumn, 'email'))
            $sType = 'Email';
        elseif (strstr($this->_sColumn, 'password'))
            $sType = 'Password';
        elseif (strstr($this->_sColumn, 'url'))
            $sType = 'Url';
        elseif (strstr($this->_sColumn, 'phone'))
            $sType = 'Phone';
        elseif (strstr($this->_sColumn, 'date'))
            $sType = 'Date';
        elseif (strstr($this->_sColumn, 'color'))
            $sType = 'Color';
        elseif (strstr($this->_sColumn, 'number'))
            $sType = 'Number';
        elseif (strstr($this->_sColumn, 'range'))
            $sType = 'Range';
        elseif (strstr($this->_sColumn, 'height'))
            $sType = 'Height';
        elseif (strstr($this->_sColumn, 'weight'))
            $sType = 'Weight';
        else
            $sType = 'Textbox';

        return $sType;
    }
}
