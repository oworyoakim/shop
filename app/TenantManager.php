<?php


namespace App;


use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\View;


class TenantManager
{

    /**
     * @var Tenant|null
     */
    private $tenant;


    public function setTenant(?Tenant $tenant)
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * @param string $subdomain
     *
     * @return Tenant|null
     */
    public function resolveTenant(string $subdomain) : ?Tenant
    {
        // get tenant using subdomain
        $tenant = ShopHelper::getTenantBySubdomain($subdomain);
        $this->tenant = $tenant;
        return $this->tenant;
    }

    public function loadGlobalSettings(){
        if($this->tenant && $settings = ShopHelper::getTenantSettings($this->tenant->id)) {
            $data = $settings->getAttributes();
            View::share($data);
        }
        return $this;
    }
}
