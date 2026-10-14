<!DOCTYPE html>
<html lang="eu">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saioa asi</title>
  <link rel="stylesheet" href="../css/logina.css">
</head>

<body>
  <main class="login">

    <!--LOGOA-->
    <img src="../data/images/logosant.png" alt="LHFP Santurtzi">
    <h1>Saioa asi</h1>

    <!--SAIOA ASI-->
    <form action="login.php" method="post">
      <label for="user">Erabiltzailea</label>
      <input type="text" id="user" name="user" placeholder="Sartu erabiltzailea" autocomplete="username" required>

      <label for="password">Pasahitza</label>
      <input type="password" id="password" name="password" placeholder="Sartu pasahitza" autocomplete="current-password" required>

      <button type="submit">Sartu</button>
    </form>

  </main>
</body>

</html>