<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/styles/main.css" rel="stylesheet">
    <link href="/public/styles/navbar.css" rel="stylesheet">
    <link href="/public/styles/user-management.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
</head>
<body>
    <nav>
        <?php include 'navbar.php'; ?>
    </nav>

    <main>
        <h1>Control Panel</h1>
        <form method="GET" action="/user-management" style="margin-bottom: 20px; text-align: center;">
            <input type="text" name="nickname" placeholder="Szukaj po nicku" value="<?= htmlspecialchars($_GET['nickname'] ?? '') ?>" style="padding: 8px; width: 200px; border-radius: 6px; border: 1px solid #ccc;">
            <button type="submit" style="margin-left: 10px;">Szukaj</button>
        </form>        
        <?php if (!empty($users)): ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Nickname</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                            <td data-label="Nickname"><?= htmlspecialchars($user['nickname']) ?></td>
                            <td data-label="Role"><?= htmlspecialchars($user['role']) ?></td>
                            <td data-label="Action">
                                <?php if ($user['email'] !== $_SESSION['user']): ?>
                                    <form method="POST" action="/delete-user" onsubmit="return confirm('Na pewno usunąć tego użytkownika?');">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php else: ?>
                                    (Twój profil)
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
             </div>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
