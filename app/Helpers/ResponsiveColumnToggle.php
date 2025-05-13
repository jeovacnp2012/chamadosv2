<?php

if (!function_exists('responsiveColumnToggle')) {
    /**
     * Gera classes para esconder colunas em mobile/desktop.
     *
     * @param bool $hideInMobile Oculta visualmente a coluna em telas menores que 640px
     * @param bool $hideInDesktop Oculta visualmente a coluna em telas maiores que 640px
     * @return array
     */
    function responsiveColumnToggle(
        bool $hideInMobile = false,
        bool $hideInDesktop = false
    ): array {
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
