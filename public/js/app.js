(function ($, window, document) {
  var endpoints = {
    test: 'https://requestb.in/17y68fv1',
    balance: 'https://bs.josephting.my/api/balance',
    orders: 'https://bs.josephting.my/api/open_orders',
  };

  var key = '';
  var secret = '';
  var clientId = '';

  var generateSignature = function (key, secret, clientId) {
    var milliseconds = (new Date()).getTime();
    var string = milliseconds + clientId + key;
    var shaObj = new JsSHA('SHA-256', 'TEXT');
    shaObj.setHMACKey(secret, 'TEXT');
    shaObj.update(string);
    var signature = shaObj.getHMAC('HEX').toUpperCase();

    return {
      nonce: milliseconds,
      signature: signature,
    };
  };

  $('#loginSave').on('click', function (e) {
    key = $('#loginKey').val();
    secret = $('#loginSecret').val();
    clientId = $('#loginClientId').val();
    $('#login').modal('hide');
  });

  $('#main').on('click', '#loadBalance', function () {
    var cred = generateSignature(key, secret, clientId);
    $.ajax({
      url: endpoints.balance,
      method: 'POST',
      headers: {
        'Api-Token': 'token',
      },
      data: {
        key: key,
        nonce: cred.nonce,
        signature: cred.signature,
      },
      beforeSend: function () {
        $('#loadBalance').prop('disabled', true);
      },

      complete: function () {
        $('#loadBalance').prop('disabled', false);
      },

      success: function (data) {
        $('#balanceResult').html('');
        var html = '';
        for (var k in data) {
          html += '<tr><th>' + k + '</th><td>' + data[k] + '</td></tr>';
        }

        $('#balanceResult').html(html);
      },
    });
  }).on('click', '#loadOrders', function () {
    var cred = generateSignature(key, secret, clientId);
    $.ajax({
      url: endpoints.orders,
      method: 'POST',
      headers: {
        'Api-Token': 'token',
      },
      data: {
        key: key,
        nonce: cred.nonce,
        signature: cred.signature,
      },
      beforeSend: function () {
        $('#loadOrders').prop('disabled', true);
      },

      complete: function () {
        $('#loadOrders').prop('disabled', false);
      },

      success: function (data) {
        $('#ordersResult').html('');
        var html = '';
        for (var k in data) {
          html += '<tr><th>' + k + '</th><td>' + data[k] + '</td></tr>';
        }

        $('#ordersResult').html(html);
      },
    });
  });

  $('#test').on('click', function (e) {
    $.ajax({
      url: endpoints.test,
      success: function (data) {
        $('#result').text(data);
      },
    });
  });
})(window.jQuery, window, document);
