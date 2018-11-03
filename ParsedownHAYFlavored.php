<?php
class ParsedownHAYFlavored extends ParsedownExtra
{
    const version = '0.1.2';

    function __construct()
    {
        $this->InlineTypes['-'][]= 'Checkbox';
        $this->InlineTypes['-'][]= 'Radio';
        $this->InlineTypes['['][]= 'Survey';
        $this->inlineMarkerList .= '-';
        $this->inlineMarkerList .= '[';
    }

    protected function blockList($Line, ?array $CurrentBlock = NULL)
    {
        if (preg_match('/^- ?\[([xX ]?)\](?::["\'](.+)["\'])? ?(.*)$/m', $Line['text']) || preg_match('/^- ?\(([xX ]?)\)(?::["\'](.+)["\'])? ?(.*)$/m', $Line['text'])) {
            return;
        } else {
            return parent::blockList($Line, $CurrentBlock);
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

    protected function inlineSurvey($excerpt)
    {
        if (preg_match("/\[survey ?(\d+) ?\/\]/i", $excerpt['text'], $matches)) {
            $random = md5(rand());

            $result = array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'allowRawHtmlInSafeMode' => true,
                    'rawHtml' => "<script id=\"" . $random . "\">getSurvey(" . $matches[1] . ", \"" . $random . "\")</script>"
                )
            );

            return $result;
        }
    }
}
