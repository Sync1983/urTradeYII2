<?php
namespace app\commands;

use yii\console\Controller;

class TDConvController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionConvertManufactures(){
      $mysql = new \mysqli('localhost','root','test','techdoc');
      if( $mysql->errno ){
        echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
      }

      $mysql->query("SET CHARACTER SET utf8");
      $mysql->set_charset('utf8');

      $SQL = "SELECT * FROM MANUFACTURERS ORDER BY 'MFA_BRAND';";
      $result = $mysql->query($SQL);
      if( $mysql->errno ){
        echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
      }

      echo PHP_EOL . count($result) . ": ";
      $cnt = 0;
      foreach ($result as $key => $row){
        $record = new \app\models\cars\ManufactureRecord();
        $record->old_id   = intval($row['MFA_ID']);
        $record->NAME     = strval($row['MFA_BRAND']);       // Полное имя
        $record->PC       = boolval($row['MFA_PC_MFC']);     // Пр-ль легковых
        $record->CV       = boolval($row['MFA_CV_MFC']);     // Пр-ль грузовых
        $record->ENG      = boolval($row['MFA_ENG_MFC']);    // Пр-ль двигателей
        $record->AXL      = boolval($row['MFA_AXL_MFC']);    // Пр-ль осей
        $record->ENG_TYPE = intval($row['MFA_ENG_TYP']);     // Тип двигателя
        $record->MODELS   = [];      // Модели
        $record->save();
        echo ".";
        $cnt ++;
      }
      echo PHP_EOL . "write count: $cnt" . PHP_EOL;
      $mysql->close();
    }

    public function actionConvertModels(){
      $mysql = new \mysqli('localhost','root','test','techdoc');
      if( $mysql->errno ){
        echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
      }

      $mysql->query("SET CHARACTER SET utf8");
      $mysql->set_charset('utf8');

      $mnf = \app\models\cars\ManufactureRecord::find()->all();
      foreach ($mnf as $mfc){
        $id = $mfc->old_id;
        $SQL = "SELECT * FROM techdoc.MODELS where MOD_MFA_ID = $id;";
        $result = $mysql->query($SQL);
        if( $mysql->errno ){
          echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
        }
        Echo "Convert MFC ".$mfc->NAME.PHP_EOL;
        $models = [];
        foreach ($result as $row){
          $start = 0;
          $end = 0;
          if( strval($row['MOD_PCON_START']) ) {
            $start = strtotime($row['MOD_PCON_START'] . "01");
          }

          if( strval($row['MOD_PCON_END']) ) {
            $end = strtotime($row['MOD_PCON_END'] . "01");
            $last_day = date('t',$end);
            $end = strtotime($row['MOD_PCON_END'] . $last_day);
          }
          /* @var $name_rslt \mysqli_result */
          $name_rslt = $mysql->query("SELECT TEX_TEXT FROM techdoc.COUNTRY_DESIGNATIONS "
            ."INNER JOIN DES_TEXTS ON TEX_ID = CDS_TEX_ID "
            ."where CDS_LNG_ID = 4  and cds_id = " . $row['MOD_CDS_ID']);
          for ($names = []; $tmp = $name_rslt->fetch_array(MYSQLI_NUM);){ $names[] = $tmp[0];}
          
          $struct = [
            'old_id'  => intval($row['MOD_ID']),
            'names'   => $names,//$row['MOD_CDS_ID'],
            'start'   => intval($start),
            'end'     => intval($end),
            'pc'      => boolval($row['MOD_PC']),
            'cv'      => boolval($row['MOD_CV']),
            'axl'     => boolval($row['MOD_AXL']),
          ];
          $models[] = $struct;
        }
        $mfc->MODELS = $models;
        $mfc->save();
      }

      $mysql->close();
    }

    public function actionConvertTypes(){
      $mysql = new \mysqli('localhost','root','test','techdoc');
      if( $mysql->errno ){
        echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
      }

      $mysql->query("SET CHARACTER SET utf8");
      $mysql->set_charset('utf8');

      $mnf = \app\models\cars\ManufactureRecord::find()->orderBy('NAME')->all();
      foreach ($mnf as $mfc){
        Echo "Convert MFC ".$mfc->NAME.PHP_EOL;

        $models = $mfc->MODELS;
        foreach ($models as $key=>$model){
          $names = implode(", ", $model['names']);
          $id = $model['old_id'];
          echo "  For models: $names" . PHP_EOL;
          $SQL = "SELECT * FROM techdoc.TYPES "
            ."INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = TYP_CDS_ID AND COUNTRY_DESIGNATIONS.CDS_LNG_ID = 4 "
            ."INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID "
            ."where typ_mod_id = $id;";
          /* @var $result \mysqli_result */
          $result = $mysql->query($SQL);
          if( $mysql->errno ){
            echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
          }
          $types = [];
          foreach ($result as $row){
            $start = 0;
            $end = 0;
            if( strval($row['TYP_PCON_START']) ) {
              $start = strtotime($row['TYP_PCON_START'] . "01");
            }

            if( strval($row['TYP_PCON_END']) ) {
              $end = strtotime($row['TYP_PCON_END'] . "01");
              $last_day = date('t',$end);
              $end = strtotime($row['TYP_PCON_END'] . $last_day);
            }
            $new_type = [
              'old_id'    => $row['TYP_ID'],                            // => 12413     ИД
              'type_name' => $row['TEX_TEXT'],                          // => 3000      Название типа
              'names'     => $row['TYP_MMT_CDS_ID'],                    // => 541014334 Название подходящих моделей
              'start'     => $start,                                    //              Начало пр-ва
              'end'       => $end,                                      //              Окончание пр-ва
              'kw'        => intval($row['TYP_KW_FROM'  ]),             // => 103       Мощность в кВт
              'hp'        => intval($row['TYP_HP_FROM'  ]),             // => 140       Мощность в лс
              'ccm'       => intval($row['TYP_CCM'      ]),             // => 2994      Объем двигателя
              'cyl'       => intval($row['TYP_CYLINDERS']),             // => 6         Цилиндров
              'door'      => $row['TYP_DOORS'],                         // =>           Дверей
              'tank'      => $row['TYP_TANK'],                          // =>           Бак
              'weight'    => floatval($row['TYP_MAX_WEIGHT']),          // =>           Максимальный вес (грузовики)

              'volt'      => intval($row['TYP_KV_VOLTAGE_DES_ID']),     // =>           Напряжение
              'abs'       => intval($row['TYP_KV_ABS_DES_ID'    ]),     // =>           АБС
              'asr'       => intval($row['TYP_KV_ASR_DES_ID'    ]),     // => 64514     АСР
              'eng'       => intval($row['TYP_KV_ENGINE_DES_ID' ]),     // => 47790     Тип двигателя
              'brk_t'     => intval($row['TYP_KV_BRAKE_TYPE_DES_ID']),  // =>           Тип тормозов
              'brk_s'     => intval($row['TYP_KV_BRAKE_SYST_DES_ID']),  // => 64325     Система тормозов
              'fuel'      => $row['TYP_KV_FUEL_DES_ID'],                // => 80846     Вид топлива
              'catl'      => $row['TYP_KV_CATALYST_DES_ID'],            // =>           Вид катализатора
              'body'      => $row['TYP_KV_BODY_DES_ID'],                // => 130781    Вид конструкции
              'model'     => $row['TYP_KV_MODEL_DES_ID'],               // =>           Вид сборки
              'axl'       => $row['TYP_KV_AXLE_DES_ID'],                // =>           Вид осей

              'ccm_tax'   => intval($row['TYP_CCM_TAX']),               // =>           Технический объем
              'ccm_ltr'   => floatval($row['TYP_LITRES']),              // => 3         Объем двигателя (литры)
              'drive'     => intval($row['TYP_KV_DRIVE_DES_ID']),       // => 97641     Вид привода
              'trans'     => intval($row['TYP_KV_TRANS_DES_ID']),       // =>           Вид трансмисии
              'fuel_sup'  => intval($row['TYP_KV_FUEL_SUPPLY_DES_ID']), // => 15069     Заправка
              'valve'     => intval($row['TYP_VALVES']),                // => 2         Количество клапанов
            ];
            ksort($new_type);
            $types[] = $new_type;
          }
          $model['types'] = $types;
          $models[$key] = $model;
        }
        $mfc->MODELS = $models;
        $mfc->save();
      }

      $mysql->close();
    }

    public function actionConvertTypeText(){
      $mysql = new \mysqli('localhost','root','test','techdoc');
      if( $mysql->errno ){
        echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
      }

      $mysql->query("SET CHARACTER SET utf8");
      $mysql->set_charset('utf8');

      $mnf = \app\models\cars\ManufactureRecord::find()->orderBy('NAME')->all();
      foreach ($mnf as $mfc){
        Echo "Convert MFC " . $mfc->NAME . PHP_EOL;
        $models = $mfc->MODELS;
        $new_models = $this->convertModels($mysql, $models);        
        $mfc->MODELS = $new_models;
        $mfc->save();        
      }
      $mysql->close();
    }

    protected function convertModels($mysql,$models){
      $ids = ["abs","asr","axl","body","brk_s","brk_t","catl","drive","eng",
              "fuel","fuel_sup","model","names","tank","trans","volt"];

      foreach ($models as $key => $model){
        $types = $model['types'];
        foreach ($types as $t_key => $type){
          $fnct = function($value) use ($type){
            return intval($type[$value]);
          };
          $ids_list = array_map($fnct, $ids);
          $ids_str = implode(",",$ids_list);
          $SQL =   "SELECT * FROM techdoc.DESIGNATIONS "
                  ."INNER JOIN DES_TEXTS ON TEX_ID = DES_TEX_ID "
                  ."where DES_LNG_ID = 16  and des_id in ($ids_str)";
          /* @var $result \mysqli_result */
          $result = $mysql->query($SQL);
          if( $mysql->errno ){
            echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
          }
          $key_text = [];
          foreach ($result as $row){
            $key_text[intval($row['DES_ID'])] = $row['TEX_TEXT'];
          }
          foreach ($ids as $id_name){
            $type[$id_name] = \yii\helpers\ArrayHelper::getValue($key_text, intval($type[$id_name]), $type[$id_name]);
          }
          $SQL = "SELECT TEX_TEXT FROM techdoc.COUNTRY_DESIGNATIONS
                  INNER JOIN DES_TEXTS ON TEX_ID = CDS_TEX_ID
                  where CDS_LNG_ID = 4  and cds_id=".intval($type['names']);
          /* @var $result \mysqli_result */
          $name_rslt = $mysql->query($SQL);
          if( $mysql->errno ){
            echo "Ошибка соединения MySql: " . $mysql->error . PHP_EOL;
          }
          for ($names = []; $tmp = $name_rslt->fetch_array(MYSQLI_NUM);){ $names[] = $tmp[0];}          
          $type['names'] = $names;
          $types[$t_key] = $type;
        }

        $model['types'] = $types;
        $models[$key] = $model;
      }
      return $models;
          /*$type["abs"       ];      //NumberLong(0),
          $type["asr"       ];      //NumberLong(0),
          $type["axl"       ];      //null,
          $type["body"      ];      //"31034",
          $type["brk_s"     ];      //NumberLong(64325),
          $type["brk_t"     ];      //NumberLong(64324),
          $type["catl"      ];      //null,
          $type["drive"     ];      //NumberLong(80839),
          $type["eng"       ];      //NumberLong(47790),
          $type["fuel"      ];      //"80846",
          $type["fuel_sup"  ];      //NumberLong(15069),
          $type["model"     ];      //null,
          $type["names"     ];      //"541011000",
          $type["tank"      ];      //null,
          $type["trans"     ];      //NumberLong(64327),
          $type["volt"      ];      //NumberLong(0),
          */
    }
    
}
