async function getData() {
    try {
        const response = await fetch(`/entrepot/get/1`);

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log("Données reçues:", data);
    } catch (error) {
        console.error("Erreur lors de la requête:", error);
    }
}
async function postData() {
    try {
        const response = await fetch('/entrepot/verif', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF
            },
            body: JSON.stringify({
                libelle: $(".libelle").val(),
                api: $(".api").val(),
                region: $(".region").val(),
                zip_code: $(".zip").val()
            })
        });

        if (!response.ok) {
            return false;
        }

        const data = await response.json();
        return data;
    } catch (error) {
        return error;
    }
}