<?php
require_once 'config.php';
require_once 'functions.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// total
if ($q !== '') {
    $like = "%{$q}%";
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM articles WHERE title LIKE ? OR description LIKE ?");
    $stmt->bind_param('ss', $like, $like);
} else {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM articles");
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$total_pages = max(1, ceil($total / $per_page));

// fetch articles
if ($q !== '') {
    $stmt = $mysqli->prepare("SELECT id, title, description, image, video, upload_date FROM articles WHERE title LIKE ? OR description LIKE ? ORDER BY upload_date DESC LIMIT ?, ?");
    $stmt->bind_param('ssii', $like, $like, $offset, $per_page);
} else {
    $stmt = $mysqli->prepare("SELECT id, title, description, image, video, upload_date FROM articles ORDER BY upload_date DESC LIMIT ?, ?");
    $stmt->bind_param('ii', $offset, $per_page);
}
$stmt->execute();
$res = $stmt->get_result();
$articles = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edukasi Kesehatan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Agar card body selalu menempel di bawah dan tombol "Baca Selengkapnya" tetap di bawah */
    .card-body {
      display: flex;
      flex-direction: column;
    }
    .card-text {
      flex-grow: 1;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Edukasi Kesehatan</a>
    <form class="d-flex ms-auto" method="get" action="index.php">
      <input class="form-control me-2" type="search" placeholder="Cari artikel..." aria-label="Search" name="q" value="<?= e($q) ?>">
      <button class="btn btn-outline-light" type="submit">Cari</button>
    </form>
  </div>
</nav>

<div class="container">
  <div class="row">
    <?php if(count($articles) === 0): ?>
      <div class="col-12"><div class="alert alert-info">Tidak ada artikel ditemukan.</div></div>
    <?php endif; ?>

    <?php foreach($articles as $a): ?>
      <div class="col-md-6 mb-4">
        <div class="card h-100">
          <div class="ratio ratio-16x9">
            <?php if ($a['image']): ?>
              <img src="uploads/<?= e($a['image']) ?>" class="card-img-top" style="object-fit:cover; width:100%; height:100%;" alt="<?= e($a['title']) ?>">
            <?php elseif ($a['video']): 
              $vid = extract_youtube_id($a['video']);
              if ($vid): ?>
                <iframe src="https://www.youtube.com/embed/<?= e($vid) ?>" title="<?= e($a['title']) ?>" allowfullscreen></iframe>
              <?php endif;
            endif; ?>
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= e($a['title']) ?></h5>
            <p class="card-text"><?= e(mb_strimwidth(strip_tags($a['description']), 0, 180, '...')) ?></p>
            <a href="detail.php?id=<?= $a['id'] ?>" class="mt-auto btn btn-primary">Baca Selengkapnya</a>
          </div>
          <div class="card-footer text-muted small">
            <?= date('d M Y H:i', strtotime($a['upload_date'])) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for($p=1;$p<=$total_pages;$p++): ?>
        <li class="page-item <?= $p==$page ? 'active' : '' ?>">
          <a class="page-link" href="index.php?page=<?= $p ?><?= $q !== '' ? '&q='.urlencode($q) : '' ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <footer class="mt-5 mb-5 text-center">
    <p class="text-muted">© <?= date('Y') ?> Edukasi Kesehatan</p>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
