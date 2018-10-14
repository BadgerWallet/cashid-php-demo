if (window.attachEvent) {window.attachEvent('onload', startBadgerWallet);}
else if (window.addEventListener) {window.addEventListener('load', startBadgerWallet, false);}
else {document.addEventListener('load', startBadgerWallet, false);}

function startBadgerWallet() {
  var badgerElements = document.body.getElementsByClassName("badger-wallet")
  for (var i = 0; i < badgerElements.length; i++) {
    var badgerElement = badgerElements[i]
    var action = badgerElement.getAttribute("data-action")
    if (action && action == "cashid") {
      badgerSubscribeCashId(badgerElement)
    } else {
      badgerSubscribeTransfer(badgerElement)
    }
  }

  function badgerSubscribeCashId(badgerElement) {
    badgerElement.addEventListener('click', function(event) {
      if (typeof web4bch !== 'undefined') {
        web4bch = new Web4Bch(web4bch.currentProvider)

        var request = badgerElement.getAttribute("data-cashid-request")
        if (!request) return
        web4bch.bch.sign(web4bch.bch.defaultAccount, request, function(err, res) {
          if (err) return

          var successCallback = badgerElement.getAttribute("data-success-callback")
          if (successCallback) {
            window[successCallback](res)
          }
        })
      } else {
        window.open('https://badgerwallet.cash/#/install')
      }
    })
  }

  function badgerSubscribeTransfer(badgerElement) {
    badgerElement.addEventListener('click', function(event) {
      if (typeof web4bch !== 'undefined') {
        web4bch = new Web4Bch(web4bch.currentProvider)

        var txParams = {
          to: badgerElement.getAttribute("data-to"),
          from: web4bch.bch.defaultAccount,
          value: badgerElement.getAttribute("data-satoshis")
        }
        web4bch.bch.sendTransaction(txParams, (err, res) => {
          if (err) return
          
          var paywallId = badgerElement.getAttribute("data-paywall-id")
          if (paywallId) {
            var paywall = document.getElementById("paywall")
            paywall.style.display = "block"
          }

          var successCallback = badgerElement.getAttribute("data-success-callback")
          if (successCallback) {
            window[successCallback](res)
          }
        })
      } else {
        window.open('https://badgerwallet.cash/#/install')
      }
    })
  }
}