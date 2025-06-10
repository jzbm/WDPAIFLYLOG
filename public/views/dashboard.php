<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/styles/navbar.css" rel="stylesheet">
    <link href="/public/styles/main.css" rel="stylesheet">
    <link href="/public/styles/dashboard.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>    <script src="/public/js/comments.js" defer></script>
    <script src="/public/js/ajax-comments.js" defer></script>
    <script src="/public/js/delete-comment.js" defer></script>
    <script src="/public/js/like.js" defer></script>
    <script src="/public/js/toggleAddPost.js" defer></script>  
    <script src="/public/js/editor.js" defer></script>
    <title>DASHBOARD</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="dashboard-layout">
        <section class="posts-section">
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
                            <a href="/messages?user=<?= $post->getUserId(); ?>">
                                <img class="avatar" src="<?= htmlspecialchars($post->getAvatar()); ?>" alt="avatar">
                            </a>
                            <div class="author-meta">
                                <a href="/messages?user=<?= $post->getUserId(); ?>">
                                    <p class="nickname"><?= htmlspecialchars($post->getNickname()); ?></p>
                                </a>
                                <p class="post-date">
                                    <?php
                                        $created = $post->getCreatedAt();
                                        echo $created ? date('d.m.Y, H:i', strtotime($created)) : '';
                                    ?>
                                </p>
                            </div>
                        </div>

                        <?php
                            $postId = $post->getId();
                            $visibleComments = $visibleCommentsMap[$postId] ?? [];
                            $hiddenCount = $hiddenCountMap[$postId] ?? 0;
                        ?>
                        <?php if (!empty($visibleComments) || !empty($hiddenCommentsMap[$postId])): ?>
                            <div class="comments-container">                                <?php foreach ($visibleComments as $comment): ?>
                                    <div class="comment" data-comment-id="<?= $comment->getId(); ?>">                                        <div class="comment-author">
                                            <div class="comment-author-info">
                                                <a href="/messages?user=<?= $comment->getUserId(); ?>">
                                                    <img class="avatar" src="<?= htmlspecialchars($comment->getAvatar()); ?>" alt="avatar">
                                                </a>
                                                <div class="author-meta">
                                                    <a href="/messages?user=<?= $comment->getUserId(); ?>">
                                                        <p class="nickname"><?= htmlspecialchars($comment->getNickname()); ?></p>
                                                    </a>                                <p class="comment-date">
                                    <?php
                                        $commentDate = $comment->getCreatedAt();
                                        echo $commentDate ? date('d.m.Y, H:i', strtotime($commentDate)) : '';
                                    ?>
                                </p>
                                                </div>
                                            </div>
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                <button type="button" class="delete-comment-btn" onclick="deleteComment(<?= $comment->getId(); ?>, this)" title="Delete comment">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <p><?= htmlspecialchars($comment->getContent()); ?></p>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (!empty($hiddenCommentsMap[$postId])): ?>                                    <?php foreach ($hiddenCommentsMap[$postId] as $comment): ?>
                                        <div class="comment hidden-comment" data-comment-id="<?= $comment->getId(); ?>">                                            <div class="comment-author">
                                                <div class="comment-author-info">
                                                    <a href="/messages?user=<?= $comment->getUserId(); ?>">
                                                        <img class="avatar" src="<?= htmlspecialchars($comment->getAvatar()); ?>" alt="avatar">
                                                    </a>
                                                    <div class="author-meta">
                                                        <a href="/messages?user=<?= $comment->getUserId(); ?>">
                                                            <p class="nickname"><?= htmlspecialchars($comment->getNickname()); ?></p>
                                                        </a>
                                                        <p class="comment-date"><?php echo $commentDate = $comment->getCreatedAt() ? date('d.m.Y, H:i', strtotime($comment->getCreatedAt())) : ''; ?></p>
                                                    </div>
                                                </div>
                                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                    <button type="button" class="delete-comment-btn" onclick="deleteComment(<?= $comment->getId(); ?>, this)" title="Delete comment">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <p><?= htmlspecialchars($comment->getContent()); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($hiddenCount > 0): ?>
                                <button class="show-more-btn" onclick="toggleComments(this, <?= $hiddenCount; ?>)">
                                    Show more comments: <?= $hiddenCount; ?>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <form method="POST" action="/add-comment">
                                <input type="hidden" name="post_id" value="<?= $post->getId(); ?>">
                                <textarea name="content" placeholder="Add a comment..." required></textarea>
                                <button type="submit">Add comment</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </section>

        <div class="right-column">
            <section class="stats-section">
                <h2>Global Flight Statistics</h2>
                <?php if (!empty($stats)): ?>
                <table class="stats-table">
                    <tr>
                        <th>Total flights:</th>
                        <td><?= htmlspecialchars($stats['total_flights']) ?></td>
                    </tr>
                    <tr>
                        <th>Flights last 7 days:</th>
                        <td><?= htmlspecialchars($stats['flights_last_7_days']) ?></td>
                    </tr>
                    <tr>
                        <th>Top airport:</th>
                        <td><?= htmlspecialchars($stats['most_used_airport']) ?></td>
                    </tr>
                    <tr>
                        <th>Top aircraft:</th>
                        <td><?= htmlspecialchars($stats['most_used_aircraft']) ?></td>
                    </tr>
                    <?php if (!empty($stats['top_pilot_nickname'])): ?>
                    <tr>
                        <th>Top pilot:</th>
                        <td>
                            <?= htmlspecialchars($stats['top_pilot_nickname']) ?>
                            <small>(<?= htmlspecialchars($stats['top_pilot_total_minutes']) ?> min)</small>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php else: ?>
                    <p class="no-stats">No statistics available.</p>
                <?php endif; ?>
            </section>

            <?php if (isset($_SESSION['user'])): ?>
            <div class="add-post-container">
                <button id="toggle-post-form" class="toggle-form-btn">Add new post</button>
                <div id="post-form-container" class="post-container">
                    <h2>ADD POST</h2>
                    <form method="POST" action="/add-post" onsubmit="submitForm()" enctype="multipart/form-data">
                        <input type="text" name="title" placeholder="Title" required />
                        <div class="editor-toolbar">
                            <button type="button" class="editor-btn" onclick="formatText('bold')"><b>B</b></button>
                            <button type="button" class="editor-btn" onclick="formatText('italic')"><i>I</i></button>
                            <button type="button" class="editor-btn" onclick="formatText('underline')"><u>U</u></button>
                            <button type="button" class="editor-btn" onclick="createLink()">🔗</button>
                        </div>
                        <div id="editor" class="empty" contenteditable="true" data-placeholder="Add content..."></div>
                        <input type="hidden" name="content" id="content" required>
                        <input type="file" name="image" accept="image/*">
                        <button id="postSubmitButton" type="submit">Add Post</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
