<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerUserPolicies();
        $this->registerGroupPolicies();
        $this->registerRolePolicies();
        $this->registerBranchPolicies();
        $this->registerMaterialPolicies();
        $this->registerFlavourCategoryPolicies();
        $this->registerFlavourPolicies();
        $this->registerProductCategoriesPolicies();
        $this->registerProductPolicies();
        $this->registerOrderTypePolicies();
        $this->registerOrderStatusPolicies();
        $this->registerPaymentTypePolicies();
        $this->registerConfigurationPolicies();
        $this->registerOrderPolicies();
        $this->registerSalesPolicies();
        $this->registerBakemanPolicies();
        //
    }

    public function registerUserPolicies()
    {

        Gate::define('create-user',function ($user){

            if($user->hasAccess(['create-user']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;

        });
        Gate::define('view-user',function ($user){

            return $user->hasAccess(['view-user']);
        });
        Gate::define('update-user',function ($user){

            if($user->hasAccess(['update-user']) && (getenv('IS_SERVER') !==false && env('IS_SERVER')==1)){
                return  true;
            }
            return false;
        });
        Gate::define('delete-user',function ($user){

            if($user->hasAccess(['delete-user']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerGroupPolicies()
    {

        Gate::define('create-group',function ($user){

            if($user->hasAccess(['create-group']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-group',function ($user){

            return $user->hasAccess(['view-group']);
        });
        Gate::define('update-group',function ($user){

            if($user->hasAccess(['update-group']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-group',function ($user){

            if($user->hasAccess(['delete-group']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerBranchPolicies()
    {

        Gate::define('create-branch',function ($user){

            if($user->hasAccess(['create-branch']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-branch',function ($user){

            return $user->hasAccess(['view-branch']);
        });
        Gate::define('update-branch',function ($user){

            if($user->hasAccess(['update-branch']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-branch',function ($user){

            if($user->hasAccess(['delete-branch']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerRolePolicies()
    {

        Gate::define('create-role',function ($user){

            if($user->hasAccess(['create-role']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-role',function ($user){


            if($user->hasAccess(['view-role']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('update-role',function ($user){


            if($user->hasAccess(['update-role']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-role',function ($user){


            if($user->hasAccess(['delete-role']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerMaterialPolicies()
    {

        Gate::define('create-material',function ($user){

            if($user->hasAccess(['create-material']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-material',function ($user){


            if($user->hasAccess(['view-material'])){
                return  true;
            }
            return false;
        });
        Gate::define('update-material',function ($user){


            if($user->hasAccess(['update-material']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-material',function ($user){


            if($user->hasAccess(['delete-material']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerFlavourCategoryPolicies()
    {

        Gate::define('create-flavourCategory',function ($user){

            if($user->hasAccess(['create-flavourCategory']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-flavourCategory',function ($user){


            if($user->hasAccess(['view-flavourCategory']) ){
                return  true;
            }
            return false;
        });
        Gate::define('update-flavourCategory',function ($user){


            if($user->hasAccess(['update-flavourCategory']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-flavourCategory',function ($user){


            if($user->hasAccess(['delete-flavourCategory']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }

    public function registerFlavourPolicies()
    {

        Gate::define('create-flavour',function ($user){

            if($user->hasAccess(['create-flavour']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-flavour',function ($user){


            if($user->hasAccess(['view-flavour'])){
                return  true;
            }
            return false;
        });
        Gate::define('update-flavour',function ($user){


            if($user->hasAccess(['update-flavour']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-flavour',function ($user){


            if($user->hasAccess(['delete-flavour']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerProductCategoriesPolicies()
    {

        Gate::define('create-category',function ($user){

            if($user->hasAccess(['create-category']) && env('IS_SERVER')==1){
//
                return  true;
            }
            return false;
        });
        Gate::define('view-category',function ($user){


            if($user->hasAccess(['view-category']) ){
                return  true;
            }
            return false;
        });
        Gate::define('update-category',function ($user){


            if($user->hasAccess(['update-category']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-category',function ($user){


            if($user->hasAccess(['delete-category']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }

    public function registerProductPolicies()
    {

        Gate::define('create-product',function ($user){

            if($user->hasAccess(['create-product']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-product',function ($user){


            if($user->hasAccess(['view-product']) ){
                return  true;
            }
            return false;
        });
        Gate::define('update-product',function ($user){


            if($user->hasAccess(['update-product']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-product',function ($user){


            if($user->hasAccess(['delete-product']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerOrderTypePolicies()
    {

        Gate::define('create-orderType',function ($user){

            if($user->hasAccess(['create-orderType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-orderType',function ($user){


            if($user->hasAccess(['view-orderType'])&& env('IS_SERVER')==1 ){
                return  true;
            }
            return false;
        });
        Gate::define('update-orderType',function ($user){


            if($user->hasAccess(['update-orderType'])&& env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-orderType',function ($user){


            if($user->hasAccess(['delete-orderType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerOrderStatusPolicies()
    {

        Gate::define('create-orderStatus',function ($user){

            if($user->hasAccess(['create-orderStatus']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-orderStatus',function ($user){


            if($user->hasAccess(['view-orderStatus']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('update-orderStatus',function ($user){


            if($user->hasAccess(['update-orderStatus']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-orderStatus',function ($user){


            if($user->hasAccess(['delete-orderStatus']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerPaymentTypePolicies()
    {

        Gate::define('create-paymentType',function ($user){

            if($user->hasAccess(['create-paymentType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-paymentType',function ($user){


            if($user->hasAccess(['view-paymentType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('update-paymentType',function ($user){


            if($user->hasAccess(['update-paymentType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-paymentType',function ($user){


            if($user->hasAccess(['delete-paymentType']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }
    public function registerConfigurationPolicies()
    {

        Gate::define('create-configuration',function ($user){

            if($user->hasAccess(['create-configuration']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-configuration',function ($user){


            if($user->hasAccess(['view-configuration']) ){
                return  true;
            }
            return false;
        });
        Gate::define('update-configuration',function ($user){


            if($user->hasAccess(['update-configuration']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('delete-configuration',function ($user){


            if($user->hasAccess(['delete-configuration']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });

        Gate::define('create-local-configuration',function ($user){


            if($user->hasAccess(['create-local-configuration']) ){
                return  true;
            }
            return false;
        });


    }
    public function registerOrderPolicies()
    {

        Gate::define('create-order',function ($user){

            if($user->hasAccess(['create-order']) &&  env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });
        Gate::define('view-order',function ($user){


            if($user->hasAccess(['view-order']) ){
                return  true;
            }
            return false;
        });
        Gate::define('update-order',function ($user){


            if($user->hasAccess(['update-order'])){
                return  true;
            }
            return false;
        });
        Gate::define('edit-order',function ($user){


            if($user->hasAccess(['edit-order'])){
                return  true;
            }
            return false;
        });
        Gate::define('delete-order',function ($user){


            if($user->hasAccess(['delete-order']) && env('IS_SERVER')==1){
                return  true;
            }
            return false;
        });


    }

    public function registerSalesPolicies()
    {

        Gate::define('view-sales',function ($user){

            if($user->hasAccess(['view-sales'])){
                return  true;
            }
            return false;
        });



    }
    public function registerBakemanPolicies()
    {

        Gate::define('bakeman-view',function ($user){

            if($user->hasAccess(['bakeman-view'])){
                return  true;
            }
            return false;
        });

        Gate::define('bakeman-update',function ($user){

            if($user->hasAccess(['bakeman-update'])){
                return  true;
            }
            return false;
        });


    }


}
