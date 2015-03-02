<?php
/**
 * Description of MainMenuWidget
 *
 * @author Sync<atc58.ru>
 */
namespace app\components;

use yii\bootstrap\Widget;
use app\models\forms\SearchForm;

class SearchWidget extends Widget{
  /** @var SearchForm **/
  public $form;  
  
  public function init() {
    parent::init();
    if(!$this->form){
      $this->form = new SearchForm();      
    }
  }

  public function run(){    
    $this->getView()->registerCssFile("/css/search_menu.css");
    return $this->render("search_widget",['form'=>  $this->form]);
  }
}
