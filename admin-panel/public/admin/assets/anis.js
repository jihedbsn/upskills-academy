function previewImage(input, previewID) {
    console.log(previewID);
    console.log(input.id)
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewID).innerHTML = `
                <img src="${e.target.result}" width="100" style="margin-top:10px; border-radius:5px;">
                <button type="button" onclick="removeImage('${input.id}', '${previewID}')" style="display:block; margin-top:5px; background:red; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">
                    Supprimer
                </button>
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(inputID, previewID) {
    let previewElement = document.getElementById(previewID);
    let textInput = previewElement.previousElementSibling; // Trouve l'input texte avant le previewID
    console.log(textInput.value)

    // Vérifie si l'élément trouvé est bien un input[type="text"]
    if (textInput && textInput.tagName === "INPUT" && textInput.type === "text") {
        textInput.value = "";
    }


    previewElement.innerHTML = "";
    document.getElementById(inputID).value = "";
}

function previewColor(event, previewID) {
    let input = event.target;
    let preview = document.getElementById(previewID);

    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
}

function previewColorPrincipale(event, previewID) {
    let input = event.target;
    let preview = document.getElementById(previewID);

    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
}