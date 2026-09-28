function getData() {

}
data = null;
async function verifProduct(ref, entrepot) {
    let nb = 0;
    await $.ajax({
        url: '/api/article/' + ref + "/" + entrepot,
        type: 'GET',

        success: function(response) {

            if (response.erreur) {
                nb = 0;
            }
            if (response.product) {
                data = response;
                console.log(response.stock, 'stock')
                nb = response.stock;

            }


        },
        error: function(xhr, status, error) {

            nb = 0;
        }
    });
    return nb;
}
async function searchProduct() {
    $(".er-affiche").html("");
    $(".btn-submit-search").attr("disabled", true);
    $(".response").html("");
    let entrepot = $(".entrepots").val();
    let ref = $(".ref").val();
    if (ref != "") {
        await $.ajax({
            url: '/api/article/' + ref + "/" + entrepot,
            type: 'GET',

            success: function(response) {
                console.log(response)
                if (response.erreur) {
                    $(".er-affiche").html(response.erreur);
                }
                if (response.product) {
                    data = response;
                    let ret = `<table class="table">
                                        <tr>
                                            <td>Code barre</td>
                                            <td>Produits</td>
                                            <td>Stock</td>
                                            <td>Taille</td>
                                            <td>Couleur</td>
                                            <td>Prix</td>
                                            <td>Action</td>
                                        </tr>

                                          <tr>
                                            <td>${response.product.sousProducts[0].code_a_barre}</td>
                                           <td>${response.product.title}</td>
                                           <td><span class='badge badge-glow bg-primary stock${response.product.id}'>${parseInt(response.stock)}</span></td>
                                             <td>${response.product.sousProducts[0].taille}</td>
                                              <td>${response.product.sousProducts[0].couleur}</td>
                                           <td>${response.product.prix} TND</td>
                                            <td>
                                                <button class='btn btn-info' onclick='addProduct(${response.product.id},${parseInt(response.stock)});'>add</button>
                                            </td>
                                        </tr>
                                 </table>`;
                    $(".response").html(ret);
                }
                $(".btn-submit-search").attr("disabled", false);

            },
            error: function(xhr, status, error) {
                $(".btn-submit-search").attr("disabled", false);

            }
        });
    } else {
        $(".btn-submit-search").attr("disabled", true);
    }

}
i = 0;

function addProduct(id, stock) {
    let table = $(".ligne_commande").html();
    if ($(".stock" + id).html() > 0) {
        stock = parseInt($(".stock" + id).html());
        stock = stock - 1;
        /*********** add ligne */
        let response = data;
        i++;
        let ligne = `
                                        <tr class="contenu-form tr${i}">
                                              <td><button type="button" class="btn-close" onclick="deleteLigne(${i},${id},${response.product.prix})">×</button>
                                               
                                              </td>
                                              <td><input type="text" class="code_barre" value="${response.product.sousProducts[0].code_a_barre}" name="code_barre" hidden>${response.product.sousProducts[0].code_a_barre}</td>
                                              <td> <input class="title" value="${response.product.title}" name="title" hidden>${response.product.title}</td>
                                              <td>1</td>
                                              <td><input class="taille" value="${response.product.sousProducts[0].taille}" name="taille" hidden>${response.product.sousProducts[0].taille}</td>
                                              <td><input class="couleur" value="${response.product.sousProducts[0].couleur}" name="taille" hidden>${response.product.sousProducts[0].couleur}</td>
                                              <td><input class="prix" value="${response.product.prix}" name="prix" hidden>${response.product.prix} TND</td>
                                              <td> <div style="float:right"><span class="badge bg-success">Nouvelle article</span></div></td>
                                        </tr>
                                 `;


        $(".ligne_commande").append(ligne);
        $(".stock" + id).html(stock);
        addArticle(response.product.prix);
        if (stock < 1) {

            $(".stock" + id).addClass('badge badge-glow bg-danger');
        }
    } else {
        $(".stock" + id).addClass('badge badge-glow bg-danger');
    }

}

function deleteLigne(i, id, prix) {
    stock = parseInt($(".stock" + id).html());
    stock = stock + 1;
    $(".stock" + id).html(stock);
    $(".tr" + i).remove();
    deleteArticle(prix);
}

async function confrimAnnulation(event) {

    $(".erreur-anis").html("");
    let i = 0;
    tab = [];
    $(".contenu-form").each(function(index, element) {
        i++;
        $(element).find("input").each(function(inputIndex, inputElement) {
            if ($(inputElement).attr('class') == "code_barre") {
                $($(inputElement).attr('name', "code_barre" + i));
                let code_barre = $(inputElement).val();
                tab.push({ code_barre: code_barre, qnt: 1 });
            }
            if ($(inputElement).attr('class') == "title") {
                $($(inputElement).attr('name', "title" + i));
            }
            if ($(inputElement).attr('class') == "taille") {
                $($(inputElement).attr('name', "taille" + i));
            }
            if ($(inputElement).attr('class') == "couleur") {
                $($(inputElement).attr('name', "couleur" + i));
            }
            if ($(inputElement).attr('class') == "prix") {
                $($(inputElement).attr('name', "prix" + i));
            }

        });
    });
    $(".nb-add").val(i);
    let entrepot = $(".entrepots").val();
    tab = await regrouperProduits(tab);
    console.log(tab)
    let nb = 0;
    let ret_new = "";
    for (var k = 0; k < tab.length; k++) {
        let code_barre_verif = tab[k].code_barre;
        let qnt = tab[k].qnt;

        nb = await verifProduct(code_barre_verif, entrepot);
        console.log(await verifProduct(code_barre_verif, entrepot));
        if (nb >= qnt) {

        } else {
            ret_new = "..";
            $(".erreur-anis").append("<li>Le code à barre " + code_barre_verif + " a un stock inférieur à celui demandé.</li>");
            console.log("quntité incorrrecte");

        }
    }

    let ret = verifForm();
    if (ret.length > 0) {
        $(".erreur-anis").append(ret);
    }
}
$("#form-anulation").on("submit", async function(event) {


    await confrimAnnulation(event);

});

function verifForm() {
    let ret = "";
    let nom = $(".nom").val();
    let prenom = $(".prenom").val();
    let email = $(".email").val();
    let phone = $(".phone").val();
    let adresse1 = $(".adresse1").val();
    let adresse2 = $(".adresse2").val();
    let zip = $(".zip").val();
    let ville = $(".ville").val();

    // Vérification du nom
    if (nom.length < 3 || nom.length > 25) {
        ret += "<li>Nom invalide (entre 3 et 25 caractères)</li>";
    }

    // Vérification du prénom
    if (prenom.length < 3 || prenom.length > 25) {
        ret += "<li>Prénom invalide (entre 3 et 25 caractères)</li>";
    }

    // Vérification de l'email
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        ret += "<li>Email invalide</li>";
    }

    // Vérification du téléphone (au moins 10 chiffres)
    const phonePattern = /^\d{8}$/;
    if (!phonePattern.test(phone)) {
        ret += "<li>Numéro de téléphone invalide (doit contenir 8 chiffres)</li>";
    }

    // Vérification de l'adresse 1
    if (adresse1.length < 5 || adresse1.length > 100) {
        ret += "<li>Adresse 1 invalide (entre 5 et 100 caractères)</li>";
    }

    // Vérification de l'adresse 2
    if (adresse2.length < 5 || adresse2.length > 100) {
        ret += "<li>Adresse 2 invalide (entre 5 et 100 caractères)</li>";
    }
    const zipPattern = /^\d{4}$/;
    if (!zipPattern.test(zip)) {
        ret += "<li>Code postal invalide (doit contenir 5 chiffres)</li>";
    }

    if (ville.length < 3 || ville.length > 50) {
        ret += "<li>Ville invalide (entre 3 et 50 caractères)</li>";
    }


    return ret;
}


async function regrouperProduits(tab) {
    let tabRegroupe = [];

    tab.forEach(function(produit) {
        let produitExistant = tabRegroupe.find(item => item.code_barre === produit.code_barre);

        if (produitExistant) {
            produitExistant.qnt += produit.qnt;
        } else {
            tabRegroupe.push(produit);
        }
    });

    return tabRegroupe;
}

total = 0;
subtotal = 0;
livraison = 0;
$(document).ready(function() {
    $(".checkbox-row").on("change", function() {
        let prix = parseFloat($(this).data("prix")) || 0;

        if ($(this).is(":checked")) {
            subtotal -= prix;
        } else {
            subtotal += prix;
        }
        miseAjourPanier();
    });

    $(".checkbox-livraison").on("change", function() {
        let prix = 8;

        if ($(this).is(":checked")) {
            subtotal += prix;
        } else {
            subtotal -= prix;
        }
        miseAjourPanier();
    });
});

function addArticle(prix) {
    subtotal += prix;
    miseAjourPanier();
}

function deleteArticle(prix) {
    subtotal -= prix;
    miseAjourPanier();
}

function miseAjourPanier() {
    $(".new-subtotal").html(subtotal.toFixed(2) + " TND");
    total = subtotal + livraison;
    $(".new-total").html(total.toFixed(2) + " TND");
    $(".total").val(total);
    $(".subtotal").val(subtotal);
    $(".fraislivraison").val(livraison);
}