<?php
/**
 *  This file has been modified by pH7 developers team (Pierre-Henry SORIA).
 */
namespace PFBC;

use PH7\Framework\Layout\Html\Design;

/*This project's namespace structure is leveraged to autoload requested classes at runtime.*/
function Load($class)
{
    $file = __DIR__ . '/../' . str_replace('\\', PH7_DS, $class) . '.php';
    if(is_file($file))
        include $file;
}

spl_autoload_register('PFBC\Load');
if(in_array('__autoload', spl_autoload_functions()))
    spl_autoload_register('__autoload');

class Form extends Base
{

    private static $sFormId;
    private $elements = array();
    private $prefix = 'http';
    private $values = array();
    private $widthSuffix = 'px';

    protected $ajax;
    protected $ajaxCallback;
    protected $attributes;
    protected $error;
    /*jQueryUI themes can be previewed at http://jqueryui.com/themeroller/.*/
    protected $jQueryUITheme = 'smoothness';
    protected $resourcesPath;
    /*Prevents various automated from being automatically applied.  Current options for this array
    included jQuery, jQueryUI, jQueryUIButtons, focus, and style.*/
    protected $prevent = array();
    protected $view;
    protected $width;

    public function __construct($id = 'pfbc', $width = '')
    {
        self::$sFormId = $id;
        $this->configure(array(
            'width' => $width,
            'action' => basename($_SERVER['SCRIPT_NAME']),
            'id' => preg_replace("/\W/", '-', $id),
            'method' => 'post'
        ));

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            $this->prefix = 'https';

        /*The Standard view class is applied by default and will be used unless a different view is
        specified in the form's configure method*/
        if(empty($this->view))
            $this->view = new View\CStandard;

        if(empty($this->error))
            $this->error = new Error\Standard;

        $this->resourcesPath = PH7_URL_STATIC . 'PFBC';
    }

    /*When a form is serialized and stored in the session, this function prevents any non-essential
    information from being included.*/
    public function __sleep()
    {
        return array('attributes', 'elements', 'error');
    }

    public function addElement(Element $element)
    {
        $element->setForm($this);
        //If the element doesn't have a specified id, a generic identifier is applied.
        $id = $element->getID();
        if(empty($id))
            $element->setID($this->attributes['id'] . '-element-' . sizeof($this->elements));
        $this->elements[] = $element;

        /*For ease-of-use, the form tag's encytype attribute is automatically set if the File element
        class is added.*/
        if($element instanceof Element\File)
            $this->attributes['enctype'] = 'multipart/form-data';
    }

    /*Values that have been set through the setValues method, either manually by the developer
    or after validation errors, are applied to elements within this method.*/
    private function applyValues()
    {
        foreach($this->elements as $element)
        {
            $name = $element->getName();
            if(isset($this->values[$name]))
                $element->setValue($this->values[$name]);
            elseif(substr($name, -2) == '[]' && isset($this->values[substr($name, 0, -2)]))
                $element->setValue($this->values[substr($name, 0, -2)]);
        }
    }

    public static function clearErrors($id = 'pfbc')
    {
        if(!empty($_SESSION['pfbc'][$id]['errors']))
            unset($_SESSION['pfbc'][$id]['errors']);
    }

    public static function clearValues($id = 'pfbc')
    {
        if(!empty($_SESSION['pfbc'][$id]['values']))
            unset($_SESSION['pfbc'][$id]['values']);
    }

    /*This method parses the form's width property into a numeric width value and a width suffix - either px or %.
    These values are used by the form's concrete view class.*/
    public function formatWidthProperties()
    {
        if(!empty($this->width))
        {
            if(substr($this->width, -1) == '%')
            {
                $this->width = substr($this->width, 0, -1);
                $this->widthSuffix = '%';
            }
            elseif(substr($this->width, -2) == 'px')
                $this->width = substr($this->width, 0, -2);
        }
        else
        {
            /*If the form's width property is empty, 100% will be assumed.*/
            $this->width = 100;
            $this->widthSuffix = '%';
        }
    }

    public function getAjax()
    {
        return $this->ajax;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getJQueryUIButtons()
    {
        return $this->jQueryUIButtons;
    }

    public function getPrevent()
    {
        return $this->prevent;
    }

    public function getResourcesPath()
    {
        return $this->resourcesPath;
    }

    public function getErrors()
    {
        $errors = array();
        if (session_status() !== PHP_SESSION_ACTIVE)
            $errors[''] = array('Error: pH7CMS requires an active session to work properly.  Simply add session_start() to your script before any output has been sent to the browser.');
        else
        {
            $errors = array();
            $id = $this->attributes['id'];
            if(!empty($_SESSION['pfbc'][$id]['errors']))
                $errors = $_SESSION['pfbc'][$id]['errors'];
        }

        return $errors;
    }

    public static function getSessionValues($id = 'pfbc')
    {
        $values = array();
        if(!empty($_SESSION['pfbc'][$id]['values']))
            $values = $_SESSION['pfbc'][$id]['values'];
        return $values;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getWidthSuffix()
    {
        return $this->widthSuffix;
    }

    public static function isValid($id = 'pfbc', $clearValues = true)
    {
        $valid = true;
        /*The form's instance is recovered (unserialized) from the session.*/
        $form = self::recover($id);
        if(!empty($form))
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
                $data = $_POST;
            else
                $data = $_GET;

            /*Any values/errors stored in the session for this form are cleared.*/
            self::clearValues($id);
            self::clearErrors($id);

            /*Each element's value is saved in the session and checked against any validation rules applied
            to the element.*/
            if(!empty($form->elements))
            {
                foreach($form->elements as $element)
                {
                    $name = $element->getName();
                    if(substr($name, -2) == '[]')
                        $name = substr($name, 0, -2);

                    /*The File element must be handled differently b/c it uses the $_FILES superglobal and
                    not $_GET or $_POST.*/
                    if($element instanceof Element\File)
                        $data[$name] = $_FILES[$name]['name'];

                    if(isset($data[$name]))
                    {
                        $value = $data[$name];
                        if(is_array($value))
                        {
                            $valueSize = sizeof($value);
                            for($v = 0; $v < $valueSize; ++$v)
                                $value[$v] = stripslashes($value[$v]);
                        }
                        else
                            $value = stripslashes($value);
                        self::setSessionValue($id, $name, $value);
                    }
                    else
                        $value = null;

                    /*If a validation error is found, the error message is saved in the session along with
                    the element's name.*/
                    if(!$element->isValid($value))
                    {
                        self::setError($id, $element->getErrors(), $name);
                        $valid = false;
                    }
                }
            }

            /*If no validation errors were found, the form's session values are cleared.*/
            if($valid)
            {
                if($clearValues)
                    self::clearValues($id);
                self::clearErrors($id);
            }
        }
        else
            $valid = false;

        return $valid;
    }

    /**
     * @return string The ID of the form.
     */
     public static function getFormId()
     {
         return self::$sFormId;
     }

    /*This method restores the serialized form instance.*/
    private static function recover($id)
    {
        if(!empty($_SESSION['pfbc'][$id]['form']))
            return unserialize($_SESSION['pfbc'][$id]['form']);
        else
            return '';
    }

    public function render($returnHTML = false)
    {
        $this->view->setForm($this);
        $this->error->setForm($this);

        /*When validation errors occur, the form's submitted values are saved in a session
        array, which allows them to be pre-populated when the user is redirected to the form.*/
        $values = self::getSessionValues($this->attributes['id']);
        if(!empty($values))
            $this->setValues($values);
        $this->applyValues();

        $this->formatWidthProperties();

        if($returnHTML)
            ob_start();

        $this->renderCSS();
        $this->view->render();
        $this->renderJS();

        /*The form's instance is serialized and saved in a session variable for use during validation.*/
        $this->save();

        if($returnHTML)
        {
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
    }

    /*When ajax is used to submit the form's data, validation errors need to be manually sent back to the
    form using json.*/
    public static function renderAjaxErrorResponse($id = 'pfbc')
    {
        $form = self::recover($id);
        if(!empty($form))
            $form->error->renderAjaxErrorResponse();
    }

    private function renderCSS()
    {
        echo '<style scoped="scoped">';
        $this->view->renderCSS();
        $this->error->renderCSS();
        foreach($this->elements as $element)
            $element->renderCSS();
        echo '</style>';
    }

    private function renderCSSFiles()
    {
        $urls = array();
        /**
         * These files are already included by default in layout.tpl, therefore it is unnecessary to include them again.
         *
        if(!in_array('jQueryUI', $this->prevent))
            $urls[] = $this->prefix . '://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/' . $this->jQueryUITheme . '/jquery-ui.css';
         */
        foreach($this->elements as $element)
        {
            $elementUrls = $element->getCSSFiles();
            if(is_array($elementUrls))
                $urls = array_merge($urls, $elementUrls);
        }

        //*This section prevents duplicate css files from being loaded.*/
        if(!empty($urls))
        {
            $urls = array_values(array_unique($urls));
            foreach($urls as $url)
                echo '<link rel="stylesheet" href="', $url, '"/>';
        }
    }

    private function renderJS()
    {
        $this->renderJSFiles();
        echo '<script>';
        $this->view->renderJS();
        foreach($this->elements as $element)
            $element->renderJS();

        $id = $this->attributes['id'];

        echo 'jQuery(document).ready(function() {';

        /*When the form is submitted, disable all submit buttons to prevent duplicate submissions.*/
        echo 'jQuery("#', $id, '").bind("submit", function() {';
        if(!in_array('jQueryUIButtons', $this->prevent))
        {
            echo 'jQuery(this).find("button[type=submit]").button("disable");';
            echo 'jQuery(this).find("button[type=submit] span.ui-button-text").css("padding-right", "2.1em").append(\'<img class="pfbc-loading" src="data:image/gif;base64,R0lGODlhEAAQAPIAAIiIiAAAAGdnZyMjIwAAADQ0NEVFRU5OTiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" alt="Loading..."/>\');';
        }
        else
            echo 'jQuery(this).find("button[type=submit]").attr("disabled", "disabled");';
        echo '});';

        /*jQuery is used to set the focus of the form's initial element.*/
        // We don't want the focus in the form field if we are on the home page.
        if( ((new \PH7\Framework\Mvc\Request\Http)->currentUrl() != PH7_URL_ROOT ) && !in_array('focus', $this->prevent))
            echo 'jQuery("#', $id, ' :input:visible:enabled:first").focus();';

        $this->view->jQueryDocumentReady();
        foreach($this->elements as $element)
            $element->jQueryDocumentReady();

        /*For ajax, an anonymous onsubmit javascript function is bound to the form using jQuery.  jQuery's
        serialize function is used to grab each element's name/value pair.*/
        if(!empty($this->ajax))
        {
            echo 'jQuery("#', $id, '").bind("submit", {';
            $this->error->clear();
            echo <<<JS
            jQuery.ajax({
                url: "{$this->attributes["action"]}",
                type: "{$this->attributes["method"]}",
                data: jQuery("#$id").serialize(),
                success: function(response) {
                    if(typeof response != "undefined" && typeof response == "object" && response.errors) {
JS;
            $this->error->applyAjaxErrorResponse();
            echo <<<JS
                        jQuery("html, body").animate({ scrollTop: jQuery("#$id").offset().top }, 500 );
                    }
                    else {
JS;
            /*A callback function can be specified to handle any post submission events.*/
            if(!empty($this->ajaxCallback))
                echo $this->ajaxCallback, '(response);';

            echo '}';

            if(!in_array('jQueryUIButtons', $this->prevent))
            {
                echo 'jQuery("#', $id, ' button[type=submit] span.ui-button-text").css("padding-right", "1em").find("img").remove();';
                echo 'jQuery("#', $id, ' button[type=submit]").button("enable");';
            }
            else
                echo 'jQuery("#', $id, '").find("button[type=submit]").removeAttr("disabled");';

            echo <<<JS
                }
            });
            return false;
        });

JS;
        }

        echo <<<JS
    });
</script>
JS;
    }

    private function renderJSFiles()
    {
        $urls = array();
        /**
         * These files are already included by default in layout.tpl, therefore it is unnecessary to include them again.
         *
        if(!in_array("jQuery", $this->prevent))
            $urls[] = $this->_prefix . "://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js";
        if(!in_array("jQueryUI", $this->prevent))
            $urls[] = $this->_prefix . "://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js";
        */
        foreach($this->elements as $element)
        {
            $elementUrls = $element->getJSFiles();
            if(is_array($elementUrls))
                $urls = array_merge($urls, $elementUrls);
        }

        /*This section prevents duplicate css files from being loaded.*/
        if(!empty($urls))
        {
            $urls = array_values(array_unique($urls));
            foreach($urls as $url)
                echo '<script src="', $url, '"></script>';
        }
    }

    /*The save method serialized the form's instance and saves it in the session.*/
    private function save()
    {
        $_SESSION['pfbc'][$this->attributes['id']]['form'] = serialize($this);
    }

    /*Valldation errors are saved in the session after the form submission, and will be displayed to the user
    when redirected back to the form.*/
    public static function setError($id, $messages, $element = '')
    {
        if (!is_array($messages))
            $messages = array($messages);

        if (empty($_SESSION['pfbc'][$id]['errors'][$element]))
            $_SESSION['pfbc'][$id]['errors'][$element] = array();

        foreach ($messages as $message)
            $_SESSION['pfbc'][$id]['errors'][$element][] = $message;
    }

    /*setSuccess*/
    public static function setSuccess($id, $message, $element = '')
    {
         return (new Design)->setFlashMsg($message, Design::SUCCESS_TYPE);
    }

    public static function setSessionValue($id, $element, $value)
    {
        $_SESSION['pfbc'][$id]['values'][$element] = $value;
    }

    /*An associative array is used to pre-populate form elements.  The keys of this array correspond with
    the element names.*/
    public function setValues(array $values)
    {
        $this->values = array_merge($this->values, $values);
    }

}
