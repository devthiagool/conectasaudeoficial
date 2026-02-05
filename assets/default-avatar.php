<?php
// Script para gerar avatar padrão
// Se acessado via PHP, redireciona para um SVG
$svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
  <rect width="150" height="150" fill="#0077b6"/>
  <circle cx="75" cy="50" r="25" fill="white"/>
  <path d="M 40 150 Q 40 100 75 100 Q 110 100 110 150 Z" fill="white"/>
</svg>';

header('Content-Type: image/svg+xml');
echo $svg;
?>
