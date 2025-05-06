<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/styles/navbar.css" rel="stylesheet">
    <link href="/public/styles/main.css" rel="stylesheet">
    <link href="/public/styles/dashboard.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <script src="/public/js/comments.js" defer></script>
    <script src="/public/js/like.js" defer></script>

    <title>DASHBOARD</title>
</head>
<body>
        <?php include 'navbar.php'; ?>
    <main>
        <?php if (isset($_SESSION['user'])): ?>
            <div class="post-container">
                <h2>DODAJ WPIS</h2>
                <form method="POST" action="/add-post" onsubmit="submitForm()" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Tytuł" required />

                    <div class="editor-toolbar">
                        <button type="button" class="editor-btn" onclick="formatText('bold')"><b>B</b></button>
                        <button type="button" class="editor-btn" onclick="formatText('italic')"><i>I</i></button>
                        <button type="button" class="editor-btn" onclick="formatText('underline')"><u>U</u></button>
                        <button type="button" class="editor-btn" onclick="createLink()">🔗</button>
                    </div>

                    <div id="editor" class="empty" contenteditable="true" data-placeholder="Dodaj treść..."></div>
                    <input type="hidden" name="content" id="content" required>

                    <input type="file" name="image" accept="image/*">
                    <button id="postSubmitButton" type="submit">Dodaj Post</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card" id="post-<?= $post->getId(); ?>">
                    <div class="post-header">
                        <h2><?= htmlspecialchars($post->getTitle()) ?></h2>

                        <div style="display: flex; gap: 8px;">
                            <form method="POST" action="/like-post" onsubmit="event.preventDefault(); toggleLike(<?= $post->getId(); ?>, this.querySelector('.like-btn'))">
                                <input type="hidden" name="post_id" value="<?= $post->getId(); ?>">
                                <button type="submit" class="like-btn <?= $post->isLikedByUser() ? 'liked' : ''; ?>">
                                    <i class="<?= $post->isLikedByUser() ? 'fa-solid fa-heart' : 'fa-regular fa-heart' ?>"></i>
                                    <?= $post->getLikesCount(); ?>
                                </button>
                            </form>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <form method="POST" action="/delete-post" onsubmit="return confirm('Na pewno usunąć ten post?');">
                                    <input type="hidden" name="post_id" value="<?= $post->getId(); ?>">
                                    <button type="submit" class="delete-post-btn" title="Usuń post">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p><?= $post->getFormattedContent(); ?></p>

                    <?php if ($post->getImage()): ?>
                        <img src="<?= $post->getImage(); ?>" alt="Post Image" class="post-image">
                    <?php endif; ?>

                    <div class="author-info">
                        <img class="avatar" src="<?= htmlspecialchars($post->getAvatar()); ?>" alt="avatar">
                        <div class="author-meta">
                            <p class="nickname"><?= htmlspecialchars($post->getNickname()); ?></p>
                            <p class="post-date">
                                <?php
                                    $created = $post->getCreatedAt();
                                    echo $created ? date('d.m.Y, H:i', strtotime($created)) : '';
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php 
                        $comments = $post->getComments(); 
                        $totalComments = count($comments);
                    ?>
                    <?php if (!empty($comments)): ?>
                        <div class="comments-container">
                            <?php 
                                $maxVisibleComments = 2;
                                foreach ($comments as $index => $comment): 
                                    $hiddenClass = $index >= $maxVisibleComments ? 'hidden-comment' : '';
                            ?>
                                <div class="comment <?= $hiddenClass; ?>">
                                    <div class="comment-author">
                                        <img class="avatar" src="<?= htmlspecialchars($comment->getAvatar()); ?>" alt="avatar">
                                        <div class="author-meta">
                                            <p class="nickname"><?= htmlspecialchars($comment->getNickname()); ?></p>
                                            <p class="comment-date">
                                                <?php
                                                    $commentDate = $comment->getCreatedAt();
                                                    echo $commentDate ? date('d.m.Y, H:i', strtotime($commentDate)) : '';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <p><?= htmlspecialchars($comment->getContent()); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($totalComments > $maxVisibleComments): ?>
                            <button class="show-more-btn" onclick="toggleComments(this, <?= $totalComments - $maxVisibleComments; ?>)">
                                Pokaż więcej komentarzy: <?= $totalComments - $maxVisibleComments; ?>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user'])): ?>
                        <form method="POST" action="/add-comment">
                            <input type="hidden" name="post_id" value="<?= $post->getId(); ?>">
                            <textarea name="content" placeholder="Dodaj komentarz..." required></textarea>
                            <button type="submit">Dodaj komentarz</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts yet.</p>
        <?php endif; ?>
    </main>
</body>
</html>
