<?php
class ParsedownHAYFlavored extends ParsedownExtra
{
    const version = '0.1.5';

    function __construct()
    {
        $this->InlineTypes['-'][]= 'Checkbox';
        $this->InlineTypes['-'][]= 'Radio';
        $this->InlineTypes['['][]= 'Survey';
        $this->InlineTypes['$'][] = 'InlineLaTeX';
        $this->InlineTypes['/'][] = 'UserTag';
        $this->BlockTypes['$'][] = 'LaTeX';
        $this->BlockTypes['['][] = 'LaTeX';
        $this->inlineMarkerList .= '-';
        $this->inlineMarkerList .= '[';
        $this->inlineMarkerList .= '$';
        $this->inlineMarkerList .= '/';
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
        if (preg_match("/^\[survey ?([a-zA-Z0-9-_]{10}) ?\/\]/i", $excerpt['text'], $matches)) {
            $random = md5(rand());

            $result = array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'allowRawHtmlInSafeMode' => true,
                    'rawHtml' => "<script id=\"" . $random . "\">getSurvey(\"" . $matches[1] . "\", \"" . $random . "\")</script>"
                )
            );

            return $result;
        }
    }

    protected function inlineInlineLaTeX($excerpt)
    {
        if (preg_match('/^\$([^\$]*)\$/', $excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'span',
                    'text' => $matches[1],
                    'attributes' => array(
                        'class' => 'katexBlock',
                        'displayMode' => 'false'
                    )
                )
            );
        }
    }

    protected function inlineUserTag($excerpt)
    {
        if (preg_match('/^\/[\w#@]+(?:[\w#@\/-]+)?/i', $excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'span',
                    'text' => $matches[0],
                    'attributes' => array(
                        'class' => 'usertag_unprocessed'
                    )
                )
            );
        }
    }

    protected function blockLaTeX($line, $block)
    {
        if (preg_match('/^(\$\$\$|\[LaTeX\])/i', $line['text'], $matches))
        {
            return array(
                'char' => $line['text'][0],
                'element' => array(
                    'name' => 'div',
                    'text' => '',
                    'attributes' => array(
                        'class' => 'katexBlock',
                        'displayMode' => 'true'
                    )
                )
            );
        }
    }

    protected function blockLaTeXContinue($line, $block)
    {
        if (isset($block['complete']))
        {
            return;
        }

        if (isset($block['interrupted']))
        {
            $block['element']['text'] .= "\n";
            unset($block['interrupted']);
        }

        if (preg_match('/^(\$\$\$|\[\/LaTeX\])/i', $line['text']))
        {
            $block['element']['text'] = substr($block['element']['text'], 1);
            $block['complete'] = true;
            return $block;
        }

        $block['element']['text'] .= "\n" . $line['body'];

        return $block;
    }

    protected function blockLaTeXComplete($block)
    {
        return $block;
    }
}
