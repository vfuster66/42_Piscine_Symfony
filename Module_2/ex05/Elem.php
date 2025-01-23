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

   public function validPage(): bool {
        if ($this->element !== 'html') {
            return false;
        }

        $head = null;
        $body = null;
        foreach ($this->children as $child) {
            if ($child->element === 'head') $head = $child;
            if ($child->element === 'body') $body = $child;
        }

        if (!$head || !$body || count($this->children) !== 2) {
            return false;
        }

        // Validation head
        $title = 0;
        $meta = 0;
        foreach ($head->children as $child) {
            if ($child->element === 'title') $title++;
            if ($child->element === 'meta') $meta++;
        }
        if ($title !== 1 || $meta !== 1) {
            return false;
        }

        return $this->validateElement($body);
    }

    private function validateElement(Elem $elem): bool {
        if ($elem->element === 'p' && !empty($elem->children)) {
            return false;
        }

        if ($elem->element === 'table') {
            foreach ($elem->children as $child) {
                if ($child->element !== 'tr') return false;
                foreach ($child->children as $td) {
                    if ($td->element !== 'th' && $td->element !== 'td') return false;
                }
            }
        }

        if (($elem->element === 'ul' || $elem->element === 'ol')) {
            foreach ($elem->children as $child) {
                if ($child->element !== 'li') return false;
            }
        }

        foreach ($elem->children as $child) {
            if (!$this->validateElement($child)) return false;
        }

        return true;
    }
}