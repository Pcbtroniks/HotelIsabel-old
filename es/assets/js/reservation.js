let selectCard = document.getElementById('selectCard');
let selectSPEI = document.getElementById('selectSPEI');
// let selectOXXO = document.getElementById('selectOXXO');
// let selectCash = document.getElementById('selectCash');

let paymentCard = document.getElementById('paymentCard');
let paymentSPEI = document.getElementById('paymentSPEI');
// let paymentOXXO = document.getElementById('paymentOXXO');
// let paymentCash = document.getElementById('paymentCash');

function hiddenPaymentMethods() {
  paymentCard.classList.add('d-none');
  paymentSPEI.classList.add('d-none');
  // paymentOXXO.classList.add('d-none');
  // paymentCash.classList.add('d-none');
}

selectCard.addEventListener('click', function () {
  hiddenPaymentMethods();
  paymentCard.classList.remove('d-none');
});

selectSPEI.addEventListener('click', function () {
  hiddenPaymentMethods();
  paymentSPEI.classList.remove('d-none');
});

// selectOXXO.addEventListener('click', function () {
//   hiddenPaymentMethods();
//   paymentOXXO.classList.remove('d-none');
// });
//
// selectCash.addEventListener('click', function () {
//   hiddenPaymentMethods();
//   paymentCash.classList.remove('d-none');
// });
