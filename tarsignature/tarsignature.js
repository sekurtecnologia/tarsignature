window.onload = function () {
    if (window.location.href.search('/front/ticket.form.php') !== -1) {
        $('<button id="tar-gerar-qrcode" class="btn btn-outline-secondary me-1 pe-2">QRCode</button>')
            .insertBefore('.navigationheader')
            .on('click', () => {

                const params = new URLSearchParams(window.location.search);

                const id = params.get('id');

                $.ajax({
                    method: 'GET',
                    url: `/plugins/tarsignature/front/ticket.qr.php?id=${id}`,
                    success: (res) => {
                        const data = JSON.parse(res);

                        if (data.success) {

                            glpi_alert({
                                title: "Faça a leitura do QrCode",
                                message: `<img src='data:image/jpeg;base64,${data.img}' alt='QR Code'><br>`
                            })
                        } else {
                            glpi_toast_error(data.message)
                        }
                    },
                    error: (err) => {
                        console.error(err);
                        glpi_toast_error('Erro ao tentar gerar QrCode')
                    }
                })

            })
    }
}