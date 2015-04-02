<<<<<<< HEAD
$(".out-data").addClass("cell-border compact hover nowrap order-column");
$(".out-data").DataTable({
  autoWidth: false,
  data: [],
  columns: [
    { data: 'producer', title: 'Производитель', width:'5%' },
    { data: 'articul',  title: 'Артикул',       width:'5%', type:'string' },
    { data: 'name',     title: 'Наименование',  width:'50%'},          
    { data: 'price',    title: 'Цена',          width:'5%', type:'string' },
    { data: 'shiping',  title: 'Срок',          width:'5%' },
    { data: 'count',    title: 'Наличие',       width:'5%' },
    { data: null,       title: 'В корзину',     width:'10%',sortable:false}
  ],
  paging: true,
  lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Все"] ],
  language: {
    search: "Быстрый поиск:",	
		emptyTable:     "Нет доступных для заказа позиций",
		info:           "Показаны записи с _START_ по _END_. Всего _TOTAL_",
		infoEmpty:      "Таблица пуста",
		infoFiltered:   "(filtered from _MAX_ total entries)",
		infoPostFix:    "",
		thousands:      ".",
		lengthMenu:     "Выводится _MENU_ строк на страницу",		  
		zeroRecords:    "Записей не найдено",
		paginate: {
      first:      "Начало",
			last:       "Конец",
			next:       "Следующая",
			previous:   "Предыдущая"
		  }
		},
		order: [[ 1, 'asc' ],[3,'asc'],[4,'asc']],
    createdRow: function( row, data, dataIndex ) {
      if(data.shiping*1 === 0){
        $(row).addClass("text-success");
      } else if(data.shiping*1 <2){
        $(row).addClass("text-info");
      }      
      var op = data.price+main.getActiveOverPrice()*data.price/100;      
      $(row).children("td").eq(3).text(op.toFixed(2));
      $(row).children("td").last().html("<a href=# class=\"ref-to-basket\">Добавить</a>");
    },
    rowCallback: function( row, data ) {     
      var op = data.price+main.getActiveOverPrice()*data.price/100;      
      $(row).children("td").eq(3).text(op.toFixed(2));
    }
});

=======
/* global main */

$(".out-data").addClass("cell-border compact hover nowrap order-column");
$(".out-data").DataTable({
  autoWidth: false,
  data: [],
  columns: [
    { data: 'producer', title: 'Производитель', width:'8%' },
    { data: 'articul',  title: 'Артикул',       width:'5%', type:'string' },
    { data: 'name',     title: 'Наименование',  width:'50%'},          
    { data: 'price',    title: 'Цена',          width:'5%', type:'string' },
    { data: 'shiping',  title: 'Срок',          width:'5%' },
    { data: 'count',    title: 'Наличие',       width:'5%' },
    { data: null,       title: 'В корзину',     width:'7%',sortable:false}
  ],
  paging: true,
  lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Все"] ],
  language: {
    search: "Быстрый поиск:",	
		emptyTable:     "Нет доступных для заказа позиций",
		info:           "Показаны записи с _START_ по _END_. Всего _TOTAL_",
		infoEmpty:      "Таблица пуста",
		infoFiltered:   "(filtered from _MAX_ total entries)",
		infoPostFix:    "",
		thousands:      ".",
		lengthMenu:     "Выводится _MENU_ строк на страницу",		  
		zeroRecords:    "Записей не найдено",
		paginate: {
      first:      "Начало",
			last:       "Конец",
			next:       "Следующая",
			previous:   "Предыдущая"
		  }
		},
		order: [[ 1, 'asc' ],[3,'asc'],[4,'asc']],
    createdRow: function( row, data, dataIndex ) {
      if(data.shiping*1 === 0){
        $(row).addClass("text-success");
      } else if(data.shiping*1 <2){
        $(row).addClass("text-info");
      }            
      var op = data.price*1+main.getActiveOverPrice()*data.price/100;      
      $(row).children("td").eq(3).text(op.toFixed(2));
      var elem = $("<a href=\" # \">Добавить</a>");
      elem.addClass("ref-to-basket");
      elem.click(data.id,main.addToBasket);
      $(row).children("td").last().html(elem);
    },
    rowCallback: function( row, data ) {     
      var op = data.price*1+main.getActiveOverPrice()*data.price/100;      
      $(row).children("td").eq(3).text(op.toFixed(2));
    }
});

>>>>>>> alcohol
