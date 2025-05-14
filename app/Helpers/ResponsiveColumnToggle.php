<?php

if (!function_exists('responsiveColumnToggle')) {
    function responsiveColumnToggle(bool $hideInMobile = false, bool $hideInDesktop = false): array
    {
        $classes = [];

        if ($hideInMobile) {
            $classes[] = 'hidden';
            $classes[] = 'sm:table-cell';
        }

        if ($hideInDesktop) {
            $classes[] = 'sm:hidden';
        }

        if (!$hideInMobile && !$hideInDesktop) {
            $classes[] = 'table-cell';
        }

        return [
            'extraAttributes' => ['class' => implode(' ', $classes)],
            'extraHeaderAttributes' => ['class' => implode(' ', $classes)],
        ];
    }
}
