<?php

namespace xavoc\securityservices;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xavoc_securityservices';

    function setup_admin(){
        $this->routePages('xavoc_secserv');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('../shared/apps/xavoc/securityservices/');

        $m = $this->app->top_menu->addMenu('SECURITY SERVICES');
        $m->addItem(['Services','icon'=>'fa fa-check-square-o'],'xavoc_secserv_billingservices');
        $m->addItem(['Clients','icon'=>'fa fa-check-square-o'],'xavoc_secserv_clients');
        $m->addItem(['Labours','icon'=>'fa fa-check-square-o'],'xavoc_secserv_labours');
        $m->addItem(['Invoices','icon'=>'fa fa-check-square-o'],'xavoc_secserv_invoices');
        $m->addItem(['Payments','icon'=>'fa fa-check-square-o'],'xavoc_secserv_payments');
        return $this;
    }

    function setup_frontend(){
        $this->routePages('xavoc_secserv');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/securityservices/');
        return $this;
    }

}
