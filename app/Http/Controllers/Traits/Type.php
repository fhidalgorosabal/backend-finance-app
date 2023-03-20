<?php

namespace App\Http\Controllers\Traits;

trait Type
{
    /**
     * Show type in spanish
     *
     * @param   string $type
     * @return  string
     */
    private function getTypeName($type) {
        return $type === 'Expense' ? 'gastos' : 'ingresos';
    }
}
