//AJAX + Валидации имени и почты, маска для телефона
$('#phone-input').inputmask("+7 (999) 999-99-99"); // Добавление маски для телефона
$('.main-form form').submit(function(event) {
    event.preventDefault(); // Отменяем стандартное поведение отправки формы
  
    // Валидация имени
    const nameValue = $('#name-input').val().trim();
    const russianNameRegex = /^(?:[А-ЯЁа-яё][А-ЯЁа-яё\s]*)(?:\s[А-ЯЁа-яё][А-ЯЁа-яё\s]*)*(?:\s[А-ЯЁа-яё][А-ЯЁа-яё\s]*)?(?:\s[А-ЯЁа-яё][А-ЯЁа-яё\s]*)?$/;
    const englishNameRegex = /^(?:[A-Za-z][A-Za-z\s]*)(?:\s[A-Za-z][A-Za-z\s]*)*(?:\s[A-Za-z][A-Za-z\s]*)?(?:\s[A-Za-z][A-Za-z\s]*)?$/;
    if (!russianNameRegex.test(nameValue) && !englishNameRegex.test(nameValue)) {
      $('#name-input').css('border-color', 'var(--red-color)');
      console.error('Invalid name format');
      return; // Прекращаем отправку формы, если имя недопустимое
    }
    $('#name-input').css('border-color', 'var(--input-color-border)');

    // Валидация почты
    const emailValue = $('#email-input').val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailValue)) {
      $('#email-input').css('border-color', 'var(--red-color)');
      console.error('Invalid email format');
      return; // Прекращаем отправку формы, если почта недопустимая
    }
    $('#email-input').css('border-color', 'var(--input-color-border)');

    // Проверка состояния флажка checkbox
    const checkboxChecked = $('#check').prop('checked');
    if (!checkboxChecked) {
        $('#check ~ label').addClass('not_correct');
        console.error('Checkbox is not checked');
        return; // Прекращаем отправку формы, если флажок не отмечен
    }
    $('#check ~ label').removeClass('not_correct');
    const phoneForm = $('#phone-input').val().trim();
    const nameForm = $('section.main-form .titleblock p').text().trim();

    var data = "name-form-modal=" + nameValue + "&email-form-modal=" + emailValue + "&phone=" + phoneForm + "&name-form=" + nameForm;
  
    // AJAX-отправка формы
    $.ajax({
      url: '/local/templates/edisontheme/ajax/form_ajax.php',
      type: 'POST',
      data: data,
      success: function(response) {
        if (response === 'success') {
            console.log('Form submitted successfully');
            $('#valid_form').text('Отправлено!');
            $('#valid_form').css('color', 'green');
            setTimeout(function(){
                $('#valid_form').text('');
                $('#valid_form').css('color', '');
            }, 3000);
        } else {
            console.error('Form submission failed on file');
            $('#valid_form').text('Не удалось отправить');
            $('#valid_form').css('color', 'red');
        }
      },
      error: function(xhr) {
        console.error('Form submission failed');
        $('#valid_form').text('Не удалось отправить');
        $('#valid_form').css('color', 'red');
      }
    });
  });
