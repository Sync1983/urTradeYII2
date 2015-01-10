<?php

return [
    'adminEmail' => 'sales@atc58.ru',
    'providerUse' =>[
      app\models\search\ProviderOnline::class,
      app\models\search\ProviderArmtek::class,      
      app\models\search\ProviderForum::class,      
    ],
    'providers'  => [
      app\models\search\ProviderOnline::class=> [
            'login'     => '6957659777',
            'password'  => 'kdV2N5iD5w'
      ],      
       app\models\search\ProviderArmtek::class=> [
            'dir'     => '/prices/armtek',
      ],       
       app\models\search\ProviderForum::class=> [
            'dir'     => '/prices/forum',
      ],       
      
      /*  [ 'name'  =>  'Ixora',
          'default_params' => [
            'login'=>'AVTOTEHS',
            'pass'=>'6de6b09l',
            'contract_id'=>'86951'
        ]
      ]*/
    ]      
];
