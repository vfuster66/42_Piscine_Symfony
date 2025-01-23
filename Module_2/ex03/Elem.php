<?php
class Elem {
   private string $element;
   private ?string $content;
   private array $children = [];
   private static array $validTags = [
       'meta', 'img', 'hr', 'br', 'html', 'head', 
       'body', 'title', 'h1', 'h2', 'h3', 'h4', 
       'h5', 'h6', 'p', 'span', 'div'
   ];

   public function __construct(string $element, ?string $content = null) {
       if (!in_array($element, self::$validTags)) {
           throw new Exception("Invalid HTML tag: $element");
       }
       $this->element = $element;
       $this->content = $content;
   }

   public function pushElement(Elem $elem) {
       $this->children[] = $elem;
   }

   public function getHTML(): string {
       $html = "<{$this->element}>";
       
       if ($this->content) {
           $html .= $this->content;
       }
       
       foreach ($this->children as $child) {
           $html .= $child->getHTML();
       }

       return $html . "</{$this->element}>";
   }
}