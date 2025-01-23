<?php
class Elem {
    private string $element;
    private ?string $content;
    private array $children = [];
    private array $attributes;
    
    private static array $validTags = [
        'meta', 'img', 'hr', 'br', 'html', 'head', 
        'body', 'title', 'h1', 'h2', 'h3', 'h4', 
        'h5', 'h6', 'p', 'span', 'div', 'table',
        'tr', 'th', 'td', 'ul', 'ol', 'li'
    ];
 
    public function __construct(string $element, ?string $content = null, array $attributes = []) {
        if (!in_array($element, self::$validTags)) {
            throw new MyException("Invalid HTML tag: $element");
        }
        $this->element = $element;
        $this->content = $content;
        $this->attributes = $attributes;
    }
 
    public function pushElement(Elem $elem) {
        $this->children[] = $elem;
    }
 
    private function getAttributesString(): string {
        if (empty($this->attributes)) return '';
        $attrs = [];
        foreach ($this->attributes as $key => $value) {
            $attrs[] = "$key=\"$value\"";
        }
        return ' ' . implode(' ', $attrs);
    }
 
    public function getHTML(): string {
        $html = "<{$this->element}{$this->getAttributesString()}>";
        
        if ($this->content) {
            $html .= $this->content;
        }
        
        foreach ($this->children as $child) {
            $html .= $child->getHTML();
        }
 
        return $html . "</{$this->element}>";
    }
 }