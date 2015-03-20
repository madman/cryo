<?php

namespace Core\Twig\Filter;

/**
 * Class RemoveDangerousContentFilter - remove scripts, frames etc from the sting
 * @package Core\Twig\Filter
 */
class RemoveDangerousContentFilter extends \Twig_SimpleFilter
{
    public function __construct($options = [])
    {
        parent::__construct($this->getName(), $this->getCallable(), $options);
    }

    public function getName()
    {
        return 'remove_dangerous_content';
    }

    public function getCallable()
    {
        return function ($text) {
            $text = preg_replace(
                [
                    // Remove invisible content
                    '@<head[^>]*?>.*?</head>@siu',
                    '@<style[^>]*?>.*?</style>@siu',
                    '@<script[^>]*?.*?</script>@siu',
                    '@<applet[^>]*?.*?</applet>@siu',
                    '@<noframes[^>]*?.*?</noframes>@siu',
                    '@<noscript[^>]*?.*?</noscript>@siu',
                    '@<noembed[^>]*?.*?</noembed>@siu',
                    '@<frameset[^>]*?.*?</frameset>@siu',
                    '@<frame[^>]*?.*?</frame>@siu',
                    '@<iframe[^>]*?.*?</iframe>@siu',
                ],
                [
                    '', '', '', '', '', '', '', '', '', '', '', '',
                ],
                $text);

            return $text;
        };
    }
}