<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir</title>
</head>
<body>
    <h1>Formulir Data Diri</h1>
    <form action="{{ route('post.page') }}" method="POST">
        @csrf
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required><br><br>

        <label for="nim">NIM:</label>
        <input type="text" id="nim" name="nim" required><br><br>

        <label for="prodi">Prodi:</label>
        <input type="text" id="prodi" name="prodi" required><br><br>

        <button type="submit">Kirim</button>
    </form>
</body>
</html>