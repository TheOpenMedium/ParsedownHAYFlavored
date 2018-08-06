<?php

class ParsedownHAYFlavored extends ParsedownExtra
{
    const version = '0.0.1';

    function __construct()
    {
        $this->InlineTypes['-'][]= 'Checkbox';
        $this->InlineTypes['-'][]= 'Radio';
        $this->inlineMarkerList .= '-';
    }

    protected function blockList($Line)
    {
        if (preg_match('/^- ?\[([xX ]?)\] ?(.*)$/m', $Line['text']) || preg_match('/^- ?\(([xX ]?)\) ?(.*)$/m', $Line['text'])) {
            return;
        } else {
            return parent::blockList($Line);
        }
    }

    protected function inlineCheckbox($excerpt)
    {
        if (preg_match('/^- ?\[([xX ]?)\] ?(.*)$/m', $excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'label',
                    'text' => $matches[2],
                    'attributes' => array(
                    )
                )
            );
        }
    }

    protected function inlineRadio($excerpt)
    {
        if (preg_match('/^- ?\(([xX ]?)\) ?(.*)$/m', $excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'label',
                    'text' => $matches[2],
                    'attributes' => array(
                    )
                )
            );
        }
    }
}
