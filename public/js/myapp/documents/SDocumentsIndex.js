/**
 * muestra u oculta el select de documentos de referencia
 */
function onChangeReason() {
    let e = document.getElementById("cancel_reason");
    let cReason = e.value;

    switch (cReason) {
        case "1":
            document.getElementById('uuids_div').value = 2;
            document.getElementById("uuids_div").style.display = "block";
            break;

        case "2":
        case "3":
        case "4":
            document.getElementById("uuids_div").style.display = "none";
            document.getElementById('uuids_div').value = 2;
            break;

        default:
            break;
    }
}