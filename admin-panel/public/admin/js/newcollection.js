i = 0;
tab = [];
const validImageExtensions = ["image/png", "image/jpg", "image/jpeg", "image/webp"];
const validVideoExtensions = ["video/mp4", "video/webm", "video/ogg"];
const maxImageSize = 1 * 1024 * 1024;
const maxVideoSize = 8 * 1024 * 1024;

function previewImages(event) {
    const files = event.target.files;
    const file = event.target.files[0];
    const imageListContainer = document.getElementById('imageList');
    if (!file) return;

    if (validImageExtensions.includes(file.type)) {
        if (file.size > maxImageSize) {
            erreur("L'image est trop volumineuse ! Taille maximale autorisée : 1 Mo.");
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
                    nouvelle: true,
                    type: 1
                });

                const src = `<img src="${img}" name='image${i}' style="width:100px" class="img-fluid">`;
                var tr = `
                <tr class="tr${i}">
                    <td>${src}</td>
                    <td><input type="text" name="lien${i}" placeholder="lien" class="form-control"></td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteImage(${i})" type="button">Supprimer</button>
                    </td>
                </tr>
            `;
                imageListContainer.innerHTML += tr;
            };

            reader.readAsDataURL(file);
        });
    } else if (validVideoExtensions.includes(file.type)) {
        if (file.size > maxVideoSize) {
            erreur("La vidéo est trop volumineuse ! Taille maximale autorisée : 8 Mo.");
            return;
        }
        Array.from(files).forEach((file) => {
            const videoURL = URL.createObjectURL(file);
            i++;
            tab.push({
                id: i,
                src: event.target.files[0],
                titre: '',
                description: '',
                nouvelle: true,
                type: 2
            });

            const videoTag = `<video controls width="100"><source src="${videoURL}" type="${file.type}"></video>`;
            var tr = `
            <tr class="tr${i}">
                <td>${videoTag}</td>
                <td><input type="text" name="lien${i}" placeholder="lien" class="form-control"></td>
                <td>
                    <button class="btn btn-danger" onclick="deleteImage(${i})" type="button">Supprimer</button>
                </td>
            </tr>
        `;
            imageListContainer.innerHTML += tr;
        });
    } else {
        erreur("Format invalide ! Veuillez sélectionner une image PNG, JPG, JPEG, WEBP ou une vidéo MP4, WEBM, OGG.");
    }
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

async function submitFormData() {
    var formData = new FormData($('.form-send')[0]);
    var tab_id = [];
    for (var i = 0; i < tab.length; i++) {
        console.log(tab[i]['id']);
        formData.append("image" + tab[i]['id'], tab[i]['src']);
        formData.append("image_old" + tab[i]['id'], tab[i]['nouvelle']);
        formData.append("type" + tab[i]['id'], tab[i]['type']);
        tab_id.push(tab[i]['id']);
    }
    formData.append('tab', JSON.stringify(tab_id));

    $.ajax({
        url: '/accueil/newcollection',
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
            setTimeout(() => {
                window.location.reload();
            }, 2000);
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
        title: "Succès!",
        text: msg,
        icon: "success",
        confirmButtonText: "OK"
    });
}