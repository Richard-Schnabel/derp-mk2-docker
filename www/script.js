let formVisible = false;

function OnLoad() {
    ShowForm();
    FilterGUI()
}

function ShowForm() {
  //změn visibility podle formVisible
  document.getElementById("add_new").style.display = formVisible ? "" : "none";

  //otoč formVisible
  formVisible = !formVisible;
}

function Filtr() {

    let dateOd = document.getElementById("fdateod").value;
    let dateDo = document.getElementById("fdatedo").value;
    let fjazyk = document.getElementById("fjazyk").value;
    let timeOd = document.getElementById("ftimeod").value;
    let timeDo = document.getElementById("ftimedo").value;
    let rateOd = document.getElementById("frateod").value;
    let rateDo = document.getElementById("fratedo").value;
    let sortBy = document.getElementById("ssortby").value;
    let sortOr = document.getElementById("ssortor").value;

    let filtr = " WHERE"
    let pocet = 0;
    let sort = Number(sortBy) + Number(sortOr);

    if (dateOd) { filtr += (pocet > 0 ? " AND" : "") + " date →= ↨" + dateOd + "↨"; pocet++; }
    if (dateDo) { filtr += (pocet > 0 ? " AND" : "") + " date ←= ↨" + dateDo + "↨"; pocet++; }
    if (fjazyk) { filtr += (pocet > 0 ? " AND" : "") + " jazyk = ↨" + fjazyk + "↨"; pocet++; }
    if (timeOd) { filtr += (pocet > 0 ? " AND" : "") + " time →= ↨" + timeOd + "↨"; pocet++; }
    if (timeDo) { filtr += (pocet > 0 ? " AND" : "") + " time ←= ↨" + timeDo + "↨"; pocet++; }
    if (rateOd) { filtr += (pocet > 0 ? " AND" : "") + " rate →= ↨" + rateOd + "↨"; pocet++; }
    if (rateDo) { filtr += (pocet > 0 ? " AND" : "") + " rate ←= ↨" + rateDo + "↨"; pocet++; }

    let adresa = "index.php?filtr=" + filtr + "&sort=" + sort + login + "";

    window.open(adresa, "_self");
}
function FilterGUI() {
    // Get the modal
    var filterModal = document.getElementById("filterGUI");

    // Get the button that opens the modal
    var filterBtn = document.getElementById("filterBtn");

    // Get the <span> element that closes the modal
    var krizek = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    filterBtn.onclick = function () {
        filterModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    krizek.onclick = function () {
        filterModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == filterModal) {
            filterModal.style.display = "none";
        }
    }
}