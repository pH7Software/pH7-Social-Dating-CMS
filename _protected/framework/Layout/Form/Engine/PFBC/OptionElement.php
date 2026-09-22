<?php
/**
 * Changes have been made by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC;

abstract class OptionElement extends Element
{
    /** @var array */
    protected $options;

    /**
     * @param string $label
     * @param string $name
     * @param array $options
     * @param array|null $properties
     */
    public function __construct($label, $name, array $options, ?array $properties = null)
    {
        $this->options = $options;
        if (!empty($this->options) &&
            array_values($this->options) === $this->options
        ) {
            $this->options = array_combine($this->options, $this->options);
        }

        parent::__construct($label, $name, $properties);

        /* Validation runs on the form saved in the session, which keeps the element's rules but not
        its options. Record the values offered, as the browser submits them, so a crafted value is
        rejected like any other invalid input. */
        $this->validation[] = new Validation\Option(array_map(
            fn ($key): string => stripslashes((string)$this->getOptionValue((string)$key)),
            array_keys($this->options)
        ));
    }

    /**
     * @param string $value
     *
     * @return bool|string
     */
    protected function getOptionValue($value)
    {
        $position = strpos($value, ':pfbc');
        if ($position !== false) {
            $value = substr($value, 0, $position);

            if ($position === 0) {
                $value = '';
            }
        }

        return $value;
    }
}
