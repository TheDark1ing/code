<script src="https://cdn.jsdelivr.net/npm/js-html2pdf@1.1.4/lib/html2pdf.min.js"></script>

<script>
    const btn = document.querySelector('.download-pdf button');
    const options = {
        filename: 'racion.pdf'
    };
    btn.addEventListener('click', () => {
        var toPrint = document.querySelector('.to-print');
        var exporter = new html2pdf(toPrint, options);
        exporter.getPdf(false).then((pdf) => {
            pdf.save();
        });
    })

    const sendEmailButton = document.querySelector('.send-email button');
    const emailInput = document.getElementById('email_user');
    const answerMail = document.getElementById('result_mail');

    //Функция для проверки валидности почты пользователя при помощи регулярного выражения
    function isValidEmail(email) {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return emailPattern.test(email);
    }

    sendEmailButton.addEventListener('click', () => {

        // Получаем введенный почтовый адрес
        const email = emailInput.value.trim();

        if (email === '') {
            emailInput.style.border = '1px solid red';
            return;
        } else {
            emailInput.style.border = '';
        }

        if (!isValidEmail(email)) {
            emailInput.style.border = '1px solid red';
            return;
        } else {
            emailInput.style.border = '';
        }

        const toPrint = document.querySelector('.to-print');
        const options = {
            filename: 'racion.pdf'
        };
        const exporter = new html2pdf(toPrint, options);

        exporter.getPdf(false).then((pdf) => {
            // Преобразовываем PDF в blob
            return pdf.output('blob');
        }).then((pdfBlob) => {
            // Создаем объект FormData для отправки файла
            const formData = new FormData();
            formData.append('pdfFile', pdfBlob, 'racion.pdf');
            formData.append('email', email);

            // Отправляем файл на сервер
            fetch('/upload-pdf.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                if (response.ok) {
                    // PDF успешно отправлен на сервер
                    answerMail.textContent = 'Файл успешно отправлен вам на почту!';
                    answerMail.style.color = 'green';
                    setTimeout(function() {
                        answerMail.textContent = '';
                    }, 5000);
                } else {
                    // Обработка ошибки
                    answerMail.textContent = 'Произошла какая-то ошибка, файл не был отправлен. Попробуйте немного позже!';
                    answerMail.style.color = 'red';
                    setTimeout(function() {
                        answerMail.textContent = '';
                    }, 5000);
                }

                // Обработка ответа сервера
                //return response.text(); // Получаем текстовое содержимое ответа
            }).then((responseData) => {
                // Выводим содержимое ответа сервера
                //console.log('Ответ от сервера:', responseData);
            }).catch((error) => {
                // Обработка ошибки
                //console.error('Произошла ошибка: ' + error);
            });
        });
    });
</script>
