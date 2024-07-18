$(document).ready(function() {
    var quantityVariable = 'quantity'; // Получаем значение PHP переменной

    $('.add-to-cart').on('click', function() {
        var productId = $(this).data('id');
        var form = $('#product-form-' + productId);
        var quantity = form.find('input[name="' + quantityVariable + '"]').val();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Обновление интерфейса
                form.hide();
                $('#quantity-controls-' + productId).show();
            },
            error: function(xhr, status, error) {
                alert('Ошибка при добавлении товара в корзину.');
            }
        });
    });

    $('.increase-quantity').on('click', function() {
        var productId = $(this).data('id');
        var userId = $(this).parent().parent().children('form').attr('data-id-more');
        var input = $('.quantity-input[data-id="' + productId + '"]');
        var newQuantity = parseInt(input.val()) + 1;

        input.val(newQuantity);
        updateCartQuantity(productId, newQuantity, userId);
    });

    $('.decrease-quantity').on('click', function() {
        var productId = $(this).data('id');
        var userId = $(this).parent().parent().children('form').attr('data-id-more');
        var input = $('.quantity-input[data-id="' + productId + '"]');
        var newQuantity = Math.max(1, parseInt(input.val()) - 1);

        input.val(newQuantity);
        updateCartQuantity(productId, newQuantity, userId);
    });

    function updateCartQuantity(productId, quantity, userId) {
        $.ajax({
            url: '/local/templates/edisontheme/ajax/update_cart_quantity.php', // Путь к файлу-обработчику
            method: 'POST',
            data: {
                action: 'update_quantity',
                product_id: productId,
                quantity: quantity,
                user_id: userId
            },
            success: function(response) {
                // Обработка успешного обновления количества
                if (response.success) {
                    console.log('Количество товара успешно обновлено');
                } else {
                    alert('Ошибка при обновлении количества товара: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Ошибка при обновлении количества товара.');
            }
        });
    }

    $('.add-to-favorites').on('click', function() {
        var productId = $(this).data('id');

        $.ajax({
            url: '/local/templates/edisontheme/ajax/favorite_handler.php',
            method: 'POST',
            data: {
                action: 'add',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    $('button[data-id="' + productId + '"]').replaceWith('<button class="remove-from-favorites" data-id="' + productId + '">Удалить из избранного</button>');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Ошибка при добавлении в избранное.');
            }
        });
    });

    $(document).on('click', '.remove-from-favorites', function() {
        var productId = $(this).data('id');

        $.ajax({
            url: '/local/templates/edisontheme/ajax/favorite_handler.php',
            method: 'POST',
            data: {
                action: 'remove',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    $('button[data-id="' + productId + '"]').replaceWith('<button class="add-to-favorites" data-id="' + productId + '">Избранное</button>');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Ошибка при удалении из избранного.');
            }
        });
    });
});