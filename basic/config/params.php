<?php

return [
    'guestOverPrice'  => 18,          //Наценка %
    'adminEmail' => 'sales@atc58.ru',
    'providerUse' =>[
      app\models\search\ProviderOnline::class,
      app\models\search\ProviderArmtek::class,      
      app\models\search\ProviderForum::class,      
    ],
    'vk_id'      => '4708880',
    'vk_secret'  => 'D1B1u4DaxiQV2JgEJxZ9',
    'fb_id'      => '908661059173455',
    'fb_secret'  => '7c5d6a78c8508031a3bcf716366c124b',
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
