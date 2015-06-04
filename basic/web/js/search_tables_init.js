/* global main */
$(".show-full").each(function (index, item){
  $(item).click(function(event){
    var item      = this;
    var parent    = $(item).parent();
    var dataJSON  = $(parent).find('script[type="text/json"]').text();
    var data      = JSON.parse(dataJSON);
    var header    = $("#full-list").find("div.modal-header");
    var table_class= $("#full-list").find(".out-data").DataTable();
    var query = {};
    var form_query = $("#search-form").serializeArray();
  
    $(header).remove(".part-loader");
    table_class.clear();
    
    function onError(data){
      console.log(data);
    }
    
    function onSuccess(data){
      var id = data.id;
      var parts = data.parts;
      
      $(header).find("div#part-loader"+id).remove();
      
      table_class.rows.add(parts).draw();
      table_class.rows().data().sort();    
      
      $("#full-list").find("a.ref-to-basket").click($().main().onAddToBasket);
    }
    
    for (var id in data){
      var value = data[ id ];
      var newLoader = $("<div></div>")
          .addClass("part-loader")
          .attr("id","part-loader"+id)
          .text(" ");
      $(header).append(newLoader);
      query = form_query;
      query.push({
        name: "maker_id",
        value: value
      });
      query.push({
        name: "provider",
        value: id
      });
      $().main().ajax("/index.php?r=site/ajax-full-load",query,onSuccess,onError);      
    }
    
    $("#full-list").modal('show');  
    
  });
});

$( ".out-data" ).addClass( "cell-border compact hover nowrap order-column" );

$( ".out-data" ).DataTable( {
  autoWidth: false,
  data: [ ],
  search: {
    smart: false  
  },
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
        var op = data.price * 1 + $().main().getActiveOverPrice() * data.price / 100;
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
    
    var op = data.price * 1 + $().main().getActiveOverPrice() * data.price / 100;
    $( row ).children( "td" ).eq( 3 ).text( op.toFixed( 2 ) );
    
  }
} 
);
