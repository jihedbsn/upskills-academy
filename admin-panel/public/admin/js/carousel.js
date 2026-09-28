i = 0;
tab = [];
const validExtensions = ["image/png", "image/jpg", "image/jpeg", "image/webp"];
const maxSize = 10 * 1024 * 1024;

function previewImages(event) {
    const files = event.target.files;
    const file = event.target.files[0];
    const imageListContainer = document.getElementById('imageList');
    if (!file) return;
    if (!validExtensions.includes(file.type)) {
        erreur("Format invalide ! Veuillez sélectionner une image PNG, JPG, JPEG ou WEBP.");
        return;
    }
    if (file.size > maxSize) {
        erreur("L'image est trop volumineuse ! Taille maximale autorisée : 10 Mo.");
        return;
    }
    Array.from(files).forEach((file) => {
        const reader = new FileReader();

        reader.onload = function(e) {
            i++;
            const img = e.target.result;
            tab.push({
                id: i,
                src: event.target.files[0],
                titre: '',
                description: '',
                nouvelle: true
            });

            const src = `<img src="${img}" name='image${i}' style="width:100px" class="img-fluid">`;
            const tr = `
            <tr class="tr${i}">
                <td>${src}</td>
                <td><input type="text" name="titre${i}" placeholder="Titre" class="form-control"></td>
                <td><input type="text" name="description${i}" class="form-control" placeholder="Description"></td>
                <td>
                    <button class="btn btn-danger" onclick="deleteImage(${i})" type="button">Supprimer</button>
                </td>
            </tr>
        `;
            imageListContainer.innerHTML += tr;
        };

        reader.readAsDataURL(file);
    });
}

function deleteImage(index) {

    const row = document.querySelector(`.tr${index}`);
    if (row) {
        row.remove();
    }
    tab.splice(index - 1, 1);
    tab = tab.map((item, idx) => ({...item, index: idx + 1 }));

}

function delete_old(index) {
    const row = document.querySelector(`.tr_${index}`);
    if (row) {
        row.remove();
    }
}
$('.form-send').submit(async function(event) {

    event.preventDefault();


    await submitFormData();
});
console.log("carousel");
async function submitFormData() {
    var formData = new FormData($('.form-send')[0]);
    var tab_id = [];
    for (var i = 0; i < tab.length; i++) {
        console.log(tab[i]['id']);
        formData.append("image" + tab[i]['id'], tab[i]['src']);
        formData.append("image_old" + tab[i]['id'], tab[i]['nouvelle']);
        tab_id.push(tab[i]['id']);
    }
    formData.append('tab', JSON.stringify(tab_id));
    $(".add-new").html('Envoi... <div class="spinner-border" style="width:15px;height:15px" role="status" aria-hidden="true"></div>');

    $.ajax({
        url: '/accueil/carousel',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        enctype: 'multipart/form-data',
        data: formData,
        contentType: false,
        processData: false,
        success: await
        function(response) {
            success('Succès ! La modification a été effectuée avec succès.');
			$(".add-new").html("Modifier carousel");
            setTimeout(() => {
                window.location.reload();
				
            }, 3000);
        },
        error: function(error) {
            alert('Erreur lors de l\'envoi du formulaire.');
            console.log(error);
        }
    });
}

function erreur(msg) {
    Swal.fire({
        title: "Erreur!",
        text: msg,
        icon: "error",
        confirmButtonText: "OK"
    });
}

function success(msg) {
    Swal.fire({
        title: "Success!",
        text: msg,
        icon: "success",
        confirmButtonText: "OK"
    });
}