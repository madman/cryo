<?php

namespace Core;

class Reflection
{

    public function __construct($subject)
    {
        $this->subject = new \ReflectionClass($subject);
    }

    /**
     * Get list of properties by tag name, like '@Field'
     * @param string\array
     * @return array
     */
    public function getPropertiesByTag($tags)
    {
        $result = [];
        if (!is_array($tags)) $tags = [$tags];

        $properties = $this->subject->getProperties();
        foreach ($properties as $property) {
            $annotate = $property->getDocComment();
            foreach ($tags as $tag) {
                if ($this->hasText($tag, $annotate)) {
                    $result[] = $property->getName();
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Make regexp like
     * /@(MultipleModel|Model)(\s+.*?)/U
     * and get tags and context from property phpDoc
     *
     * @param $property
     * @param array|string $tag_names , example: 'Model' or ['Model', 'MultipleModel']
     * @return array
     */
    public function getPropertyTags($property, $tag_names = [])
    {
        $result = [];
        if (!is_array($tag_names)) $tag_names = [$tag_names];
        if (count($tag_names) <= 0) return $result;

        $tag_names = implode('|', $tag_names);
        $text = $this->subject->getProperty($property)->getDocComment();

        $regex = "/@($tag_names)(\s+.*?)/U";
        $matches = false;
        if (preg_match_all($regex, $text, $matches)) {
            $count = count(reset($matches));
            for ($i = 0; $i < $count; $i++) {
                $tag = $matches[1][$i];
                $content = trim($matches[2][$i]);
                $result[$tag][] = $content;
            }
        }

        return $result;
    }

    protected function hasText($needle, $subject)
    {
        return strpos($subject, $needle) !== false;
    }
}