<?php if ($isHeader): ?>
<html>
<head>
<title>Simple Task Manager</title>
</head>

<body>

<nav>
    <a href="/">Nyitólap</a>
    <a href="/index.php?page=projects">Projektek</a>
    <a href="/index.php?page=tickets">Jegyek</a>
</nav>

<h1>Page: <?= htmlspecialchars($pageName) ?></h1>

<?php else: ?>

<hr />

</body>
</html>
<?php endif; ?>