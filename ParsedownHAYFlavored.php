<?php
class ParsedownHAYFlavored extends ParsedownExtra
{
    const version = '0.1.1';

    function __construct()
    {
        $this->InlineTypes['-'][]= 'Checkbox';
        $this->InlineTypes['-'][]= 'Radio';
        $this->InlineTypes['['][]= 'Survey';
        $this->inlineMarkerList .= '-';
        $this->inlineMarkerList .= '[';
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

    protected function inlineSurvey($excerpt)
    {
        if (preg_match("/\[survey ?(\d+) ?\/\]/i", $excerpt['text'], $matches)) {
            $localeList = preg_split('#[,;-]#', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            foreach ($localeList as $locale) {
                if ($locale == 'en' || $locale == 'fr') {
                    $l = $locale;
                    break;
                }
            }
            if (empty($l)) {
                $l = 'en';
            }

            $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/';
            $url = $root . $l . '/display/survey/' . $matches[1];
            $result = array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'rawHtml' => file_get_contents($url)
                )
            );

            return $result;
        }
    }
}
