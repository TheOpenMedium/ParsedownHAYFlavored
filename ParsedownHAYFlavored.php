<?php
class ParsedownHAYFlavored extends ParsedownExtra
{
    const version = '0.1.0';

    function __construct()
    {
        $this->InlineTypes['-'][]= 'Checkbox';
        $this->InlineTypes['-'][]= 'Radio';
        $this->inlineMarkerList .= '-';
    }

    protected function blockList($Line)
    {
        if (preg_match('/^- ?\[([xX ]?)\](?::["\'](.+)["\'])? ?(.*)$/m', $Line['text']) || preg_match('/^- ?\(([xX ]?)\)(?::["\'](.+)["\'])? ?(.*)$/m', $Line['text'])) {
            return;
        } else {
            return parent::blockList($Line);
        }
    }

    protected function inlineCheckbox($excerpt)
    {
        if (preg_match('/^- ?\[([xX ]?)\](?::["\'](.+)["\'])? ?(.*)$/m', $excerpt['text'], $matches))
        {
            $result = array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'label',
                    'elements' => array(
                        array('name' => 'input', 'attributes' => array('type' => 'checkbox')),
                        array('text' => $matches[3]),
                        array('name' => 'br'),
                    )
                )
            );

            if ($matches[1] == "x" || $matches[1] == "X") {
                $result["element"]["elements"][0]["attributes"]["checked"] = "";
            }

            if ($matches[2]) {
                $result["element"]["elements"][0]["attributes"]["name"] = $matches[2];
            }

            return $result;
        }
    }

    protected function inlineRadio($excerpt)
    {
        if (preg_match('/^- ?\(([xX ]?)\)(?::["\'](.+)["\'])? ?(.*)$/m', $excerpt['text'], $matches))
        {
            $result = array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'label',
                    'elements' => array(
                        array('name' => 'input', 'attributes' => array('type' => 'radio')),
                        array('text' => $matches[3]),
                        array('name' => 'br'),
                    )
                )
            );

            if ($matches[1] == "x" || $matches[1] == "X") {
                $result["element"]["elements"][0]["attributes"]["checked"] = "";
            }

            if ($matches[2]) {
                $result["element"]["elements"][0]["attributes"]["name"] = $matches[2];
            }

            return $result;
        }
    }
}
