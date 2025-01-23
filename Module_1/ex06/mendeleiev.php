<?php
function parseElement($line) {
   if (empty(trim($line))) return null;
   $data = [];
   $parts = explode('=', $line);
   $data['name'] = trim($parts[0]);
   
   $attributes = explode(',', trim($parts[1]));
   foreach ($attributes as $attr) {
       list($key, $value) = explode(':', trim($attr));
       $data[trim($key)] = trim($value);
   }
   return $data;
}

function getElementColor($number) {
   $colors = [
       'noble_gases' => '#ffcc99',
       'alkali_metals' => '#ff99cc', 
       'alkaline_earth' => '#ff99ff',
       'transition_metals' => '#99ccff',
       'lanthanides' => '#cc99ff',
       'actinides' => '#9999ff',
       'poor_metals' => '#99ffcc',
       'metalloids' => '#ffff99',
       'nonmetals' => '#ccff99',
       'hydrogen' => '#ff9999'
   ];

   if ($number == 1) return $colors['hydrogen'];
   if (in_array($number, [2, 10, 18, 36, 54, 86])) return $colors['noble_gases'];
   if (in_array($number, [3, 11, 19, 37, 55, 87])) return $colors['alkali_metals'];
   if (in_array($number, [4, 12, 20, 38, 56, 88])) return $colors['alkaline_earth'];
   if ($number >= 57 && $number <= 71) return $colors['lanthanides'];
   if ($number >= 89 && $number <= 103) return $colors['actinides'];
   if ($number >= 21 && $number <= 30) return $colors['transition_metals'];
   if (in_array($number, [5, 14, 32, 33, 51, 52])) return $colors['metalloids'];
   
   return '#f0f0f0';
}

function elementToHtml($element) {
   if (!$element) return '<td class="empty"></td>';
   
   return sprintf('
       <td class="element" style="background-color:%s">
           <div class="atomic-number">%s</div>
           <div class="symbol">%s</div>
           <div class="name">%s</div>
           <div class="mass">%.4f</div>
           <div class="electron">%s</div>
       </td>',
       getElementColor((int)$element['number']),
       $element['number'],
       $element['small'],
       $element['name'],
       floatval($element['molar']),
       $element['electron']
   );
}

function createMendeleievTable() {
   $content = file_get_contents('ex06.txt');
   $elements = array_map('parseElement', explode("\n", $content));
   
   $grid = array_fill(0, 7, array_fill(0, 18, null));
   foreach ($elements as $element) {
       if (!$element) continue;
       $row = ($element['number'] <= 56) ? 
              floor(($element['number']-1)/18) : 
              floor(($element['number']-54)/18) + 2;
       $grid[$row][(int)$element['position']] = $element;
   }
   
   $html = '<!DOCTYPE html>
<html><head><title>Periodic Table</title>
<style>
body {
   background: #1a1a1a;
   font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
   padding: 20px;
}
table {
   border-collapse: separate;
   border-spacing: 5px;
   margin: 20px auto;
   background: #1a1a1a;
}
.element {
   width: 120px;
   height: 120px;
   padding: 8px;
   border-radius: 4px;
   text-align: center;
   transition: all 0.3s ease;
   cursor: pointer;
   border: 2px solid rgba(0,0,0,0.2);
}
.element:hover {
   transform: scale(1.1);
   z-index: 1;
   box-shadow: 0 0 20px rgba(255,255,255,0.2);
}
.atomic-number {
   font-size: 14px;
   text-align: left;
   color: #000;
   font-weight: bold;
}
.symbol {
   font-size: 28px;
   font-weight: bold;
   margin: 10px 0;
   color: #000;
}
.name {
   font-size: 12px;
   color: #000;
   margin: 5px 0;
}
.mass, .electron {
   font-size: 11px;
   color: #333;
   margin-top: 3px;
}
.empty {
   width: 120px;
   height: 120px;
}
h1 {
   text-align: center;
   color: #fff;
   margin-bottom: 30px;
}
</style></head>
<body>
<h1>Tableau Périodique des Éléments</h1>
<table>';

   foreach ($grid as $row) {
       $html .= '<tr>';
       foreach ($row as $element) {
           $html .= elementToHtml($element);
       }
       $html .= '</tr>';
   }
   
   $html .= '</table></body></html>';
   file_put_contents('mendeleiev.html', $html);
}

createMendeleievTable();