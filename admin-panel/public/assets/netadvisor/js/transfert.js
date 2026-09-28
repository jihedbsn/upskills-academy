async function verif() {

}

$(document).ready(function() {
    $(".stock-boutique").change(async function() {
        var selectedValue = $(this).val();

        if (selectedValue) {
            $(".spiner-loading").css("display", "block");
            $(".stock-boutique").attr("disabled", true);
            for (var i = 0; i < ligne_commande.length; i++) {
                $(".td" + ligne_commande[i].id).html(`
                    <div class="spinner-border text-info spiner-loading" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `);
                await $.ajax({
                    url: '/api/articlebyentrepot/' + ligne_commande[i].code_barre + "/" + selectedValue,
                    type: 'GET',

                    success: function(response) {
                        console.log(response)
                        var stock_article = ligne_commande[i].qnt;
                        if (response.stock) {
                            if (response.stock >= stock_article) {
                                let html = `<span class='badge badge-glow bg-success'>${parseInt(response.stock)}</span>`;
                                $(".td" + ligne_commande[i].id).html(html);
                            } else {
                                let html = `<span class='badge badge-glow bg-danger'>${parseInt(response.stock)}</span>`;
                                $(".td" + ligne_commande[i].id).html(html);
                            }
                        } else {
                            let html = `<span class='badge badge-glow bg-danger'>erreur</span>`;
                            $(".td" + ligne_commande[i].id).html(html);
                        }


                    },
                    error: function(xhr, status, error) {


                    }
                });

            }
            $(".spiner-loading").css("display", "none");
            $(".stock-boutique").attr("disabled", false);

        }


    });
});