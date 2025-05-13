<?php
namespace App\Traits;
trait HasSuperAdminBypass
{
    public function can($ability, $arguments = [])
    {
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        return parent::can($ability, $arguments);
    }
}
