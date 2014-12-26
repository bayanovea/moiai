<?php

/**
 * Класс конструирования HTML-элементов
 */
interface IHtmlElements
{
    public function bootstrapFiles();
    public function mainCssJsFiles();
    public function flotFiles();
    public function buildStartGraphics($from, $to);
    public function main_navbar($active);
    public function settings_navbar();
    public function report_navbar();
    public function buildPagination($num, $addQuery);
}

?>