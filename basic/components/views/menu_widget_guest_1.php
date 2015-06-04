  <nav class="navbar-atc navbar-3 navbar-small">
  <?php $form = yii\bootstrap\ActiveForm::begin([
          'id'	 => 'search-form',
          'method' => 'get',
          'action' => ['site/search']
        ]);?>
    <div class="input-group">
		  <span class="input-group-btn">
			<?= $form->field($model, "history")->dropDownList($model->history);?>
		  </span>
		  <?=
		  /*Html::input("text", "search_text", $form->search_text, [
				  'class'			 => 'form-control input-medium',
				  'id'			 => 'search-string',
				  'min-size'		 => '50',
				  'size'			 => '20',
				  'placeholder'	 => "Введите номер запчасти",
				  'autocomplete'	 => 'off'
		  ])*/ "a";
		  ?>
		  <!---->
		  <span class="input-group-btn">
			<?=
			ButtonDropdown::widget([
					'label'		 => '',
					'options'	 => [
							'class' => 'btn-info',
					],
					'dropdown'	 => [
							'options'	 => [
									'class' => 'dropdown-menu-right'
							],
							'items'		 => SearchHistoryRecord::getHtmlList()
					]
			]);
			?>
    <?= "a"//Html::submitButton("Искать", ['class' => 'btn btn-info search-button', 'id' => 'search-button']); ?>
			<label for="cross" class="btn btn-info" style="padding: 4px 10px;">
			<?=
			CheckboxX::widget([
					'name'			 => 'cross',
					'options'		 => [
							'id' => 'cross',
					// 'class' => 'btn btn-info'
					],
					'value'			 => $form->cross,
					'pluginOptions'	 => ['threeState' => false]
			]);
			?>
			  Аналоги
			</label>

		<?=
		/*Html::dropDownList('over_price', $form->over_price, $form->over_price_list, [
				'id'		 => 'over-price',
				'class'		 => 'over-price selectpicker',
				'data-width' => "150px",
				//'class'     =>'over-price btn btn-info selectpicker',
				'onchange'	 => 'main.changeOverPrice();']);*/ "b"
		?>
		  </span>
		</div>
	<?php ActiveForm::end() ?>
  </nav>
</div>
