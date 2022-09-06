<?php

namespace App\Http\Middleware\Tenant;

use App\Models\Tenant\User;
use App\TenantManager;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TenantConnection {

    /**
     * @var TenantManager
     */
    private $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
         //dd($request->route()->parameter('subdomain'));
        $baseUrl = config("app.url_base");
        $host = $request->getHost();
        //dd($baseUrl, $host);
        $pos = strpos($host, $baseUrl);
        // dd($pos);
        if($pos === false) {
            abort(404);
        }
        // get the subdomain
        $subdomain = $request->route()->parameter('subdomain');
        //dd($subdomain);
        if(empty($subdomain) || strtolower($subdomain) === 'www') {
            // no subdomain OR subdomain === www,
            // redirect to the main system without switching DB connection
            $fullPath = "http://{$baseUrl}/{$request->path()}";
            return Redirect::to($fullPath);
        }

        $subdomain = strtolower($subdomain);

        //dd($subdomain);

        $tenant = $this->tenantManager->resolveTenant($subdomain);
        if(! $tenant) {
            abort(404);
        }
        if(! $tenant->authorized) {
            abort(401);
        }
        // put the subdomain in the session
        $request->session()->put('subdomain', $subdomain);
        // put the tenant in the session
        $request->session()->put('tenant', $tenant);
        // forget the subdomain from the session
        $request->route()->forgetParameter('subdomain');
        // set global scope on User model
        User::addGlobalScope('tenant', function (Builder $builder) use ($tenant){
            $builder->where('tenant_id', $tenant->id);
        });
        // load global settings
        $this->tenantManager->loadGlobalSettings();
        // continue
        return $next($request);
    }

}
