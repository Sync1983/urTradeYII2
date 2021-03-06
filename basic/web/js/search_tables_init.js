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
    table_class.page.len( -1 ).draw();
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
      width: '8%'
    },
    {data: 'name', title: 'Наименование', width: '50%'},
    {data: 'price', title: 'Цена', width: '5%',
      /*render: function (data, type, row) {        
        var op = data.price * 1 + $().main().getActiveOverPrice() * data.price / 100;
        return type === 'display'?op.toFixed( 2 ):data;
      }*/
    },
    {data: 'shiping', title: 'Срок', width: '5%',
      render: function (data, type, row) {        
        var sp = parseInt(data) + 1;        
        if( isNaN(sp) ){
          sp = 4;
        }
        return sp;//type === 'display'?sp:data;
      }},
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
    
    var articul,
        wrap,
        info,
        count;

    articul = $( row ).children( "td" ).eq(1);
    count   = $( row ).children( "td" ).eq(6);
    wrap    = $('<span></span>').addClass("search-articul-btn");
    articul.append(wrap);
    $(wrap).main().searchHelperButton();

    if ( data.shiping * 1 === 0 ) {
      $( row ).addClass( "text-success" );
    } 
    else if ( data.shiping * 1 < 2 ) {
      $( row ).addClass( "text-info" );
    }
    
    if( data.info ){
      info = $('<span></span>').addClass("info-articul-btn");
      $(info).main().infoBtnHover(data.info);
      count.append(info);      
    }
    
    if( (data.lot_quantity) && (data.lot_quantity>1) ){
      info = $('<span></span>').addClass("lot-articul-btn");
      $(info).main().infoBtnHover("Минимальная партия для заказа: " + data.lot_quantity + " шт.");
      count.append(info);
    }    
  },
  
  rowCallback: function (row, data) {
    
    var op = data.price * 1 + $().main().getActiveOverPrice() * data.price / 100;
    $( row ).children( "td" ).eq( 3 ).text( op.toFixed( 2 ) );
    
  }
} 
);
