<?php

namespace xavoc\securityservices;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xavoc_securityservices';

    function init(){
        parent::init();

        $this->app->addHook('entity_collection',[$this,'exportEntities']);
    }

    function exportEntities($app,&$array){
        $array['billing_service'] = ['caption'=>'Billing Services','type'=>'xepan\base\Basic','model'=>'xavoc\securityservices\Model_BillingService'];
        $array['client'] = ['caption'=>'Client Management','type'=>'xepan\base\Basic','model'=>'xavoc\securityservices\Model_Client'];
        $array['labour'] = ['caption'=>'Labour Management','type'=>'xepan\base\Basic','model'=>'xavoc\securityservices\Model_Labour'];
        $array['Client Record Generate'] = ['caption'=>'Client Record Management','type'=>'xepan\base\Basic','model'=>'xavoc\securityservices\Model_ClientMonthYear'];
        $array['Configuration'] = ['caption'=>'SSS Configuration','type'=>'xepan\base\Basic','model'=>'xavoc\securityservices\Model_Layout'];
    }

    function setup_admin(){
        $this->routePages('xavoc_secserv');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('../shared/apps/xavoc/securityservices/');

        $m = $this->app->top_menu->addMenu('SECURITY SERVICES');
        $m->addItem(['Services','icon'=>'fa fa-check-square-o'],'xavoc_secserv_billingservices');
        $m->addItem(['Clients','icon'=>'fa fa-check-square-o'],'xavoc_secserv_clients');
        $m->addItem(['Labours','icon'=>'fa fa-check-square-o'],'xavoc_secserv_labours');
        $m->addItem(['Monthly Records','icon'=>'fa fa-check-square-o'],'xavoc_secserv_monthrecords');
        $m->addItem(['Generate Payments','icon'=>'fa fa-check-square-o'],'xavoc_secserv_payments');
        $m->addItem(['Labour Payment','icon'=>'fa fa-check-square-o'],'xavoc_secserv_labourpayment');
        $m->addItem(['Labour PF Report','icon'=>'fa fa-check-square-o'],'xavoc_secserv_pfreport');
        $m->addItem(['Configuration','icon'=>'fa fa-cog'],'xavoc_secserv_configuration');
        return $this;
    }

    function setup_frontend(){
        $this->routePages('xavoc_secserv');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/securityservices/');
        return $this;
    }

}
