

$( document ).ready(function() {
  loadCharts();
  initInputChangeCapture();
});

function loadCharts() {

  $.each( $('.market-container'), function(index, value) {
    populateChart($(this));
  });
}

function populateChart(market) {
  var marketId = market.data('id');
  var period = market.find('.chart-inputs .time-period').val();
  $.get('/market/fetch-chart-data?id='+ marketId +'&period='+ period, function(marketData) {
      loadChart(marketId, marketData);
  });
}

function loadChart(marketId,data) {

  var chart = $('#market-'+ marketId +' .chart-container');
  var text = chart.data('market-name');
  var marketName = chart.data('market-name');

  //$('#market-'+ marketId).find('.mask').removeClass('mask');
  hideMask(marketId);

  Highcharts.setOptions({
    global : {
      useUTC : false,
    }
  });

  chart.highcharts('StockChart', {
  
    rangeSelector : {
      selected : 1,
      inputEnabled: chart.width() > 480
    },
  
    title : {
      text : text
    },
    
    series : [{
      name : marketName,
      data : data,
      tooltip: {
        valueDecimals: 9
      }
    }]
  });
}

function showMask(marketId) {
  $('#market-'+ marketId).addClass('mask');
}

function hideMask(marketId) {
  $('#market-'+ marketId).removeClass('mask');
}

function initInputChangeCapture() {

  $('.chart-input').on('change', function() {
    var marketId = $(this).data('market-id');
    showMask(marketId);
    populateChart($('#market-'+ marketId));
  });
}
