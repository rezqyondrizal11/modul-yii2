<!DOCTYPE html>
<html>
<head>
    <title>Data book</title>
    <style type="text/css">
    .page{padding:2cm;}
    table{border-spacing:0;border-collapse: collapse;width:100%;}
    table td, table th{border: 1px solid #ccc;}
    </style>
</head>
<body>    
    <div class="page">    
        <h1>Data book</h1>
        <table border="0">
        <tr>
                <th>No</th>
                <th>Title</th>
                <th>Description</th>
        </tr>
        <?php
        $no = 1;
        foreach($dataProvider->getModels() as $book){ 
        ?>
        <tr>
                <td><?= $no++ ?></td>
                
                <td><?= $book->tittle ?></td>
                <td><?= $book->description ?></td>
        </tr>
        <?php
        }
        ?>
        </table>
    </div>   
</body>
</html>
