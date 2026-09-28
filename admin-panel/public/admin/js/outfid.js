function previewFile1(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    // Vérification de la taille et du type de fichier
    if (file && ((file.type.startsWith("image/") && file.size <= 2097152) || (file.type.startsWith("video/") && file.size <= 8388608))) {
        reader.onload = function(e) {
            const fileType = file.type.startsWith("image/") ? "image" : "video";
            const fileSrc = e.target.result;

            if (fileType === "image") {
                // Affichage de l'image téléchargée
                document.getElementById('preview1').innerHTML = `<img src="${fileSrc}" style="width:100%" class="img-fluid">`;
            } else if (fileType === "video") {
                // Affichage de la vidéo téléchargée
                document.getElementById('preview1').innerHTML = `<video controls style="width: 100%;">
                    <source src="${fileSrc}" type="${file.type}">
                    Votre navigateur ne prend pas en charge la lecture de vidéos.
                </video>`;
            }
        };
        reader.readAsDataURL(file);
    } else {
        // Affichage d'une alerte si le fichier est trop lourd ou du mauvais type
        alert('Veuillez télécharger un fichier image (max 2 Mo) ou vidéo (max 8 Mo). Formats autorisés: PNG, JPG, JPEG, WEBP, MP4.');
    }
}

function previewFile2(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    // Vérification de la taille et du type de fichier
    if (file && ((file.type.startsWith("image/") && file.size <= 2097152) || (file.type.startsWith("video/") && file.size <= 8388608))) {
        reader.onload = function(e) {
            const fileType = file.type.startsWith("image/") ? "image" : "video";
            const fileSrc = e.target.result;

            if (fileType === "image") {
                // Affichage de l'image téléchargée
                document.getElementById('preview2').innerHTML = `<img src="${fileSrc}" style="width:100%" class="img-fluid">`;
            } else if (fileType === "video") {
                // Affichage de la vidéo téléchargée
                document.getElementById('preview2').innerHTML = `<video controls style="width: 100%;">
                    <source src="${fileSrc}" type="${file.type}">
                    Votre navigateur ne prend pas en charge la lecture de vidéos.
                </video>`;
            }
        };
        reader.readAsDataURL(file);
    } else {
        // Affichage d'une alerte si le fichier est trop lourd ou du mauvais type
        alert('Veuillez télécharger un fichier image (max 2 Mo) ou vidéo (max 8 Mo). Formats autorisés: PNG, JPG, JPEG, WEBP, MP4.');
    }
}

 $('.form-send').on('submit', function(e) {
	 console.log("je suis anis");
	   $('.add-new').attr("disabled",true);
	$('.add-new').css("opacity",0.8);
	   $('.add-new').html(' Envoi... <div class="spinner-border" role="status" aria-hidden="true" style="width:15px;height:15px"></div>');
 });