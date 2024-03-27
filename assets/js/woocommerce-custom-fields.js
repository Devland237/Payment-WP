var optionsPaiement = {
  "mtn": {
    "option": document.getElementById("woocommerce_viazipay_momo"),
    "field": document.getElementById("woocommerce_viazipay_fee_support_option_momo"),
    "feeSupportField": document.querySelector(".fee-support-option_momo"),
    "label" : document.querySelector(".fee_payment_option_momo")
  },
  "orange": {
    "option": document.getElementById("woocommerce_viazipay_om"),
    "field": document.getElementById("woocommerce_viazipay_fee_support_option_om"),
    "feeSupportField": document.querySelector(".fee-support-option_om"),
    "label" : document.querySelector(".fee_payment_option_om")
  },
  "coinbase": {
    "option": document.getElementById("woocommerce_viazipay_coinbase"),
    "field": document.getElementById("woocommerce_viazipay_fee_support_option_coinbase"),
    "feeSupportField": document.querySelector(".fee-support-option_coinbase"),
    "label" : document.querySelector(".fee_payment_option_coinbase")
  },
  "creditCart": {
    "option": document.getElementById("woocommerce_viazipay_cart"),
    "field": document.getElementById("woocommerce_viazipay_fee_support_option_cart"),
    "feeSupportField": document.querySelector(".fee-support-option_cart"),
    "label" : document.querySelector(".fee_payment_option_cart")
  }
};

for (var key in optionsPaiement) {
  optionsPaiement[key].field.style.display = "none";
  optionsPaiement[key].label.style.display = "none";
}

for (var key in optionsPaiement) {
  optionsPaiement[key].option.addEventListener("change", toggleFeeSupportField);
}

function toggleFeeSupportField() {
  for (var key in optionsPaiement) {
    var option = optionsPaiement[key].option;
    var field = optionsPaiement[key].field;
    var label = optionsPaiement[key].label;
    var feeSupportField = optionsPaiement[key].feeSupportField;
    
    if (option.checked) {
      field.style.display = "inline-block";
      label.style.display = "inline-block";
      feeSupportField.classList.remove("hidden");
    } else {
      field.style.display = "none";
      label.style.display = "none";
      feeSupportField.classList.add("hidden");
    }
  }
}
