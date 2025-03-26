function sanitizeEmail(email) {
    // Заменяем @ и . на _
    return email.replace(/[@.]/g, "_");
}
$(document).ready(function () {
    window.updateUser = function (email) {
        let safeEmail = sanitizeEmail(email);
        let newName = $("#name-" + safeEmail).val();
        let newRole = $("#role-" + safeEmail).val();

        console.log("Отправляем:", email, newName, newRole);

        $.ajax({
            url: './update_user.php',
            type: 'POST',
            data: {
                email: email.toLowerCase(),
                name: newName.trim(),
                role: newRole.trim()
            },
            dataType: 'json',
            success: function (response) {
                console.log("Ответ сервера:", response);
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Ошибка AJAX:", error);
                alert('Ошибка при обновлении пользователя: ' + error);
            }
        });

    };

    window.deleteUser = function (email) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        $.ajax({
            url: './delete_user.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function (response) {
                console.log("Ответ сервера:", response);
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Error deleting user: ' + error);
            }
        });
    };
});
