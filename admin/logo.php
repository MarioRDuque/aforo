<html>
<?php include_once "encabezado.php" ?>
<br>

<body>
    <div class="container">
        <div class="text-center">
            <h1>Subir Logos</h1>
            <br>
            <br>
            <br>
            <br>
            <br>
            <form enctype="multipart/form-data" action="subirarchivo.php" method="POST">
                <table class="table table-active">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><h3><input name="uploadedfile" type="file" accept="image/png, image/jpeg" />
                            <input type="submit" value="Subir archivo" /></h3></td>
                        <td></td>
                    </tr>
                </table>
            </form>

        </div>
    </div>
</body>
<?php include_once "../pie.php" ?>
</html>