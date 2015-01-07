<?php

return [
    'adminEmail' => 'sales@atc58.ru',
    'providerUse' =>[
      app\models\search\ProviderOnline::class,
    ],
    'providers'  => [
      app\models\search\ProviderOnline::class=> [
            'login'     => '6957659777',
            'password'  => 'kdV2N5iD5w'
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
