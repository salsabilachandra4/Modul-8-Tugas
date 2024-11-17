<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}

include("connection.php");

if (isset($_POST["submit"])) {
    // Mengambil data dari form
    $nim = htmlentities(strip_tags(trim($_POST["nim"])));
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));
    
    $error_message = "";

    // Validasi data (sesuaikan dengan validasi di student_add.php)
    if (empty($name)) {
        $error_message .= "- Nama belum diisi <br>";
    }
    if (empty($birth_city)) {
        $error_message .= "- Tempat lahir belum diisi <br>";
    }
    if (empty($department)) {
        $error_message .= "- Jurusan belum diisi <br>";
    }
    if (!is_numeric($gpa) OR ($gpa <=0)) {
        $error_message .= "- IPK harus diisi dengan angka";
    }

    // Jika tidak ada error, update data
    if ($error_message === "") {
        $birth_date_full = "$birth_year-$birth_month-$birth_date";

        $query = "UPDATE student SET name='$name', birth_city='$birth_city', 
            birth_date='$birth_date_full', faculty='$faculty', department='$department', gpa=$gpa WHERE nim='$nim'";

        $result = mysqli_query($connection, $query);

        if($result) {
            $message = "Data mahasiswa dengan NIM = \"$nim\" berhasil diupdate.";
            header("Location: student_view.php?message=" . urlencode($message));
        } else {
            die ("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
        }
    }
} else {
    // Mengambil data mahasiswa berdasarkan NIM untuk diedit
    if (isset($_GET['nim'])) {
        $nim = htmlentities(strip_tags($_GET['nim']));
        $query = "SELECT * FROM student WHERE nim='$nim'";
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) == 1) {
            $data = mysqli_fetch_assoc($result);
            $name = $data['name'];
            $birth_city = $data['birth_city'];
            $faculty = $data['faculty'];
            $department = $data['department'];
            $gpa = $data['gpa'];

            $birth_date_full = explode("-", $data['birth_date']);
            $birth_date = $birth_date_full[2];
            $birth_month = $birth_date_full[1];
            $birth_year = $birth_date_full[0];
        } else {
            header("Location: student_view.php");
        }
    }
}

$arr_month = [
    "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April", 
    "5" => "Mei", "6" => "Juni", "7" => "Juli", "8" => "Agustus", 
    "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$select_ftib = ($faculty == 'FTIB') ? "selected" : "";
$select_fteic = ($faculty == 'FTEIC') ? "selected" : "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div id="header">
            <h1 id="logo">Edit Data Mahasiswa</h1>
        </div>
        <hr>
        <nav>
            <ul>
                <li><a href="student_view.php">Tampil</a></li>
                <li><a href="student_add.php">Tambah</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <h2>Edit Data Mahasiswa</h2>
        <?php
        if ($error_message !== "") {
            echo "<div class='error'>$error_message</div>";
        }
        ?>
        <form id="form_mahasiswa" action="student_edit.php" method="post">
            <fieldset>
                <legend>Edit Mahasiswa</legend>
                <p>
                    <label for="nim">NIM : </label>
                    <input type="text" name="nim" id="nim" value="<?php echo $nim ?>" readonly>
                </p>
                <p>
                    <label for="name">Nama : </label>
                    <input type="text" name="name" id="name" value="<?php echo $name ?>">
                </p>
                <p>
                    <label for="birth_city">Tempat Lahir : </label>
                    <input type="text" name="birth_city" id="birth_city" value="<?php echo $birth_city ?>">
                </p>
                <p>
                    <label for="birth_date">Tanggal Lahir : </label>
                    <select name="birth_date" id="birth_date">
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            $selected = ($i == $birth_date) ? "selected" : "";
                            echo "<option value='$i' $selected>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="birth_month">
                        <?php
                        foreach ($arr_month as $key => $value) {
                            $selected = ($key == $birth_month) ? "selected" : "";
                            echo "<option value='$key' $selected>$value</option>";
                        }
                        ?>
                    </select>
                    <select name="birth_year">
                        <?php
                        for ($i = 1990; $i <= 2005; $i++) {
                            $selected = ($i == $birth_year) ? "selected" : "";
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label for="faculty">Fakultas : </label>
                    <select name="faculty" id="faculty">
                        <option value="FTIB" <?php echo $select_ftib ?>>FTIB</option>
                        <option value="FTEIC" <?php echo $select_fteic ?>>FTEIC</option>
                    </select>
                </p>
                <p>
                    <label for="department">Jurusan : </label>
                    <input type="text" name="department" id="department" value="<?php echo $department ?>">
                </p>
                <p>
                    <label for="gpa">IPK : </label>
                    <input type="text" name="gpa" id="gpa" value="<?php echo $gpa ?>" placeholder="Contoh: 2.75">
                </p>
            </fieldset>
            <br>
            <p>
                <input type="submit" name="submit" value="Update Data">
            </p>
        </form>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>