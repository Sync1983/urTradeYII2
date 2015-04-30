/* global main */

window.initCollapse = function (){  
  $("body").find("a.collapse-toggle").each(
    function( index, item ){      
      $(item).click(
        function( event ){
          if ( $(this).attr("aria-expanded") === "true" ) {
            return;
          }
          var head = $(this).parent().parent().parent();
          var dataSource = head.find('script[type="text/json"]').text();          
          var dataObject = JSON.parse(dataSource);
          main.loadParts(dataObject,head);
        } 
      );
    } 
  );
};

$( ".out-data" ).addClass( "cell-border compact hover nowrap order-column" );

$( ".out-data" ).DataTable( {
  autoWidth: false,
  data: [ ],
  columns: [
    {data: 'producer', title: 'Производитель', width: '8%'},
    {data: {
        _: 'articul',
        sort: 'data-order'
      },
      title: 'Артикул',
      width: '5%'
    },
    {data: 'name', title: 'Наименование', width: '50%'},
    {data: 'price', title: 'Цена', width: '5%',
      render: function (data, type, row) {
        var op = data.price * 1 + main.getActiveOverPrice() * data.price / 100;
        return type === 'display'?op.toFixed( 2 ):data;
      }
    },
    {data: 'shiping', title: 'Срок', width: '5%'},
    {data: 'count', title: 'Наличие', width: '5%'},
    {data: null, title: 'В корзину', width: '7%', sortable: false,
      render: function (data, type, row) {
        return '<a href="#" class="ref-to-basket">Добавить</a>';
      }
    }
  ],
  paging: true,
  lengthMenu: [ [ 25, 50, 100, -1 ], [ 25, 50, 100, "Все" ] ],
  language: {
    search: "Быстрый поиск:",
    emptyTable: "Нет доступных для заказа позиций",
    info: "Показаны записи с _START_ по _END_. Всего _TOTAL_",
    infoEmpty: "Таблица пуста",
    infoFiltered: "(filtered from _MAX_ total entries)",
    infoPostFix: "",
    thousands: ".",
    lengthMenu: "Выводится _MENU_ строк на страницу",
    zeroRecords: "По Вашему запросу ничего не найдено!",
    paginate: {
      first: "Начало",
      last: "Конец",
      next: "Следующая",
      previous: "Предыдущая"
    }
  },
  order: [ [ 1, 'asc' ], [ 3, 'asc' ], [ 4, 'asc' ] ],
  createdRow: function (row, data, dataIndex) {

    if ( data.shiping * 1 === 0 ) {
      $( row ).addClass( "text-success" );
    } 
    else if ( data.shiping * 1 < 2 ) {
      $( row ).addClass( "text-info" );
    }
  },
  rowCallback: function (row, data) {
    
    var op = data.price * 1 + main.getActiveOverPrice() * data.price / 100;
    $( row ).children( "td" ).eq( 3 ).text( op.toFixed( 2 ) );
    
  }
} 
);
